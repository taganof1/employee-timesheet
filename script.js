fetch('upload.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({ uid: employeeUID })  // Send the UID from the card
})
.then(response => response.json())
.then(data => {
    // Display success or failure based on the response
    document.getElementById('welcomeScreen').style.display = 'none';
    document.getElementById('resultScreen').style.display = 'block';

    if (data.status === 'success') {
        // Success
        document.getElementById('resultMessage').innerText = 'Clock-in successful!';
        document.getElementById('employeeDetails').style.display = 'block';
        document.getElementById('empName').innerText = 'Name: ' + data.employee.name;
        document.getElementById('empID').innerText = 'Employee ID: ' + data.employee.id;
        document.getElementById('empPosition').innerText = 'Position: ' + data.employee.position;
    } else {
        // Failure
        document.getElementById('resultMessage').innerText = data.message;
        document.getElementById('employeeDetails').style.display = 'none';
    }
})
.catch(error => console.error('Error:', error));
