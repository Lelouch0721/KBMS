<!DOCTYPE html>
<html>
<head>
    <title>Unreturned Item Notification</title>
</head>
<body>
    <h1>Reminder to Return Borrowed Item</h1>
    <p>Dear {{ $item->borrower_name }},</p>

    <p>This is a friendly reminder that you have not returned the item.</p>
    <ul>
        <li><strong>Item:</strong> {{ $item->type }} - {{ $item->item_name }}</li>
        <li><strong>Borrowed On:</strong> {{ $item->date_borrowed }}</li>
    </ul>

    <p>Please return it as soon as possible to avoid any penalties.</p>
    
    <p>Thank you!</p>
</body>
</html>
