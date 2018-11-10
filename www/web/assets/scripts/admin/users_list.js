function selectPage(pageNumber) {
  console.log(pageNumber);
//   alert(`Selected page ${pageNumber}`);
    updateUrlWithPage(pageNumber)
}

function updateUrlWithPage(pageNumber) {
    if (history.pushState) {
        var newurl =
          window.location.protocol +
          "//" +
          window.location.host +
          window.location.pathname +
          `?page=${pageNumber}`;
        window.history.pushState({ path: newurl }, "", newurl);
      }
}
