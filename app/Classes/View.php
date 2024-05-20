<?php

namespace BARTENDER\Classes;

class View
{
    private $viewPath;
    private $layout;

    public function __construct($viewPath)
    {
        $this->viewPath = $viewPath;
        $this->layout = 'layout'; // Default layout
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function render($viewName, $data = [], $withLayout = true)
    {
        if ($withLayout) {
            $viewContent = $this->getViewContent($viewName, $data);
            $layoutContent = $this->getLayoutContent($this->layout, ['content' => $viewContent]);
            return $layoutContent;
        } else {
            // Render the view without layout
            return $this->getViewContent($viewName, $data);
        }
    }

    private function getViewContent($viewName, $data)
    {
        $viewFile = $this->viewPath . DIRECTORY_SEPARATOR . $viewName . '.html';
        if (file_exists($viewFile)) {
            extract($data);
            ob_start();
            include $viewFile;
            return ob_get_clean();
        } else {
            return 'View not found';
        }
    }

    private function getLayoutContent($layoutName, $data)
    {
        $layoutFile = $this->viewPath . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . $layoutName . '.html';
        if (file_exists($layoutFile)) {
            $layoutContent = file_get_contents($layoutFile);
            return str_replace('@@content@@', $data['content'], $layoutContent);
        } else {
            return 'Layout not found';
        }
    }

    private function getViewWithComponents($viewName, $data, $components)
    {
        $viewFile = $this->viewPath . DIRECTORY_SEPARATOR . $viewName . '.html';
        if (file_exists($viewFile)) {
            // Get the content of the view file
            $viewContent = file_get_contents($viewFile);

            // Get the content of each component and replace the placeholder in the view content
            foreach ($components as $componentName => $componentData) {
                $componentFile = $this->viewPath . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $componentName . '.html';
                if (file_exists($componentFile)) {
                    extract($componentData);
                    ob_start();
                    include $componentFile;
                    $componentContent = ob_get_clean();
                    $viewContent = str_replace('@@' . $componentName . '@@', $componentContent, $viewContent);
                } else {
                    $viewContent .= 'Component ' . $componentName . ' not found';
                }
            }

            // Replace any remaining component placeholders with an empty string
            $viewContent = preg_replace('/@@[\w-]+@@/', '', $viewContent);

            // Replace other placeholders in the view content with actual data
            foreach ($data as $key => $value) {
                $viewContent = str_replace('@@' . $key . '@@', $value, $viewContent);
            }

            return $viewContent;
        } else {
            return 'View not found';
        }
    }



    public function getViewWithComponentsAndLayout($viewName, $data, $components, $layoutName)
    {
        $viewContent = $this->getViewWithComponents($viewName, $data, $components);

        $layoutFile = $this->viewPath . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . $layoutName . '.html';
        if (file_exists($layoutFile)) {
            // Get the content of the layout file
            $layoutContent = file_get_contents($layoutFile);

            // Replace the @@content@@ placeholder in the layout content with the view content
            $layoutContent = str_replace('@@content@@', $viewContent, $layoutContent);

            // Replace the @@components@@ placeholder in the layout content with the components content
            foreach ($components as $componentName => $componentData) {
                $componentFile = $this->viewPath . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $componentName . '.html';
                if (file_exists($componentFile)) {
                    extract($componentData);
                    ob_start();
                    include $componentFile;
                    $componentContent = ob_get_clean();
                    $layoutContent = str_replace('@@' . $componentName . '@@', $componentContent, $layoutContent);
                } else {
                    $layoutContent .= 'Component ' . $componentName . ' not found';
                }
            }

            // Replace any remaining component placeholders with an empty string
            $layoutContent = preg_replace('/@@[\w-]+@@/', '', $layoutContent);

            // Replace other placeholders in the layout content with actual data
            foreach ($data as $key => $value) {
                $layoutContent = str_replace('@@' . $key . '@@', $value, $layoutContent);
            }

            return $layoutContent;
        } else {
            return 'Layout not found';
        }
    }
}
