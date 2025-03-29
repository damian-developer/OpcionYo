<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>New Appointment</title>
</head>
<body>
    <h1>New Appointment</h1>
    <p>Doctor: {{ $appointment->doctor->name }}</p>
    <p>Patient: </p>
    <p>Date: {{ $appointment->date }}</p>
    <p>Hour: {{ $appointment->start }} - {{ $appointment->end }}</p>
</body>
</html>
