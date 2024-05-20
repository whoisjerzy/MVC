var processs = function (search) {
  var timeout = setTimeout(function () {
    var number = KTUtil.getRandomInt(1, 6);

    if (number === 3) {
      // Hide results
      resultsElement.classList.add("d-none");
      clearElement.classList.add("d-none");
      resultsElement.classList.remove("search-basic-resutls", "shadow");
      inputField.classList.remove("search-basic-input", "shadow");
      // Show empty message
    } else {
      // Show results
      resultsElement.classList.remove("d-none");
      clearElement.classList.remove("d-none");
      inputField.classList.add("search-basic-input", "shadow");
      resultsElement.classList.add("search-basic-resutls", "shadow");
    }

    // Complete search
    search.complete();
  }, 1500);
};

var clear = function (search) {
  // Hide results
  resultsElement.classList.add("d-none");
  inputField.classList.remove("search-basic-input", "shadow");
};

// Input handler
const handleInput = () => {
  // Select input field
  const inputField = element.querySelector("[data-kt-search-element='input']");

  // Handle keyboard press event
  inputField.addEventListener("keydown", (e) => {
    // Only apply action to Enter key press
    if (e.key === "Enter") {
      e.preventDefault(); // Stop form from submitting
    }
  });
};

// Elements
element = document.querySelector("#kt_docs_search_handler_basic");

// if (!element) {
//   return;
// }

wrapperElement = element.querySelector("[data-kt-search-element='wrapper']");
resultsElement = element.querySelector("[data-kt-search-element='results']");
clearElement = element.querySelector("[data-kt-search-element='clear']");
inputField = element.querySelector("[data-kt-search-element='input']");

// Initialize search handler
searchObject = new KTSearch(element);

// Search handler
searchObject.on("kt.search.process", processs);

// Clear handler
searchObject.on("kt.search.clear", clear);

// Handle select
KTUtil.on(element, "[data-kt-search-element='customer']", "click", function () {
  //modal.hide();
});

// Handle input enter keypress
handleInput();
