require('./bootstrap');


const consultar = (endPoint) => {
    fetch(endPoint)
    .then(response => response.json())
    .then(data => {
        drawSingleEvent(data);
        // drawAllEvents(data);

    })
    .catch(error => console.log('error', error));
}
