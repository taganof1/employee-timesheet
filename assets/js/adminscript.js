function fetchClockInData() {
    fetch('fetch-clockin-data.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('clockin-data').innerHTML = data;
        })
        .catch(error => console.error('Error fetching data:', error));
}

setInterval(fetchClockInData, 3000);
fetchClockInData();