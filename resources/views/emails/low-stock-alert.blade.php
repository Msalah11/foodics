<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Low Stock Alert</title>
</head>
<body>
<p>
    Dear {{ $merchantName }},
</p>

<p>
    This is a notification to inform you that the stock of the ingredient "{{ $ingredientName }}" has fallen below 50%.
</p>

<p>
    Please take necessary actions to restock this ingredient.
</p>

<p>
    Sincerely,<br>
    Foodics Team
</p>
</body>
</html>
