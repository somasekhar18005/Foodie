<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "canteen_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the 'action' key exists in the POST array
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add') {
        // Code to add an item
        $name = $_POST['name'];
        $price = $_POST['price'];
        $delivery_time = $_POST['delivery_time'];
        $image_url = $_POST['image_url'];

        // Prepare and execute the SQL insert query
        $stmt = $conn->prepare("INSERT INTO items (name, price, delivery_time, image_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $price, $delivery_time, $image_url);
        
        if ($stmt->execute()) {
            echo "Item added successfully.";
        } else {
            echo "Error adding item: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($action === 'delete') {
        // Code to delete an item
        $name = $_POST['name'];

        // Prepare and execute the SQL delete query
        $stmt = $conn->prepare("DELETE FROM items WHERE name = ?");
        $stmt->bind_param("s", $name);
        
        if ($stmt->execute()) {
            echo "Item deleted successfully.";
        } else {
            echo "Error deleting item: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($action === 'fetch') {
        // Code to fetch items
        $result = $conn->query("SELECT * FROM items");
        $items = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($items);
    } else {
        echo "Invalid action.";
    }
} else {
    echo "No action specified.";
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file here -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .navbar {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar h1 {
            margin: 0;
            font-size: 24px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .navbar a:hover {
            background-color: #0056b3;
        }

        .items-containers1 {
            margin-bottom: 20px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin: 10px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s;
        }

        .menu-item:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .menu-item img {
            width: 100px;
            height: 100px;
            border-radius: 5px;
            margin-right: 15px;
        }

        .add-item-form, .delete-item-form {
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .add-item-form input, .delete-item-form input {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        .add-item-form input:focus, .delete-item-form input:focus {
            border-color: #007bff;
            outline: none;
        }

        .add-item-form button, .delete-item-form button {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .add-item-form button:hover, .delete-item-form button:hover {
            background-color: #218838;
            transform: translateY(-1px);
        }

        @media (max-width: 600px) {
            .menu-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .menu-item img {
                margin-bottom: 10px;
            }

            .add-item-form input, .delete-item-form input, 
            .add-item-form button, .delete-item-form button {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <h1>Admin Dashboard</h1>
    <a href="index.php">Logout</a>
</div>

<div id="suggestionsContainer" class="items-containers1"></div>

<div class="add-item-form">
    <input type="text" id="itemName" placeholder="Item Name" required>
    <input type="text" id="itemPrice" placeholder="Item Price (₹)" required>
    <input type="text" id="itemDeliveryTime" placeholder="Delivery Time" required>
    <input type="text" id="itemImage" placeholder="Image URL" required>
    <button id="addItemBtn">Add Item</button>
</div>

<div class="delete-item-form">
    <input type="text" id="itemToDelete" placeholder="Enter Item Name to Delete" required>
    <button id="deleteItemBtn">Delete Item</button>
</div>  

<script>
    fetchItems();

    // Add item
    document.getElementById('addItemBtn').addEventListener('click', () => {
        const name = document.getElementById('itemName').value;
        const price = document.getElementById('itemPrice').value;
        const delivery_time = document.getElementById('itemDeliveryTime').value;
        const image_url = document.getElementById('itemImage').value;

        fetch('admin_dashboard.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'add',
                name: name,
                price: price,
                delivery_time: delivery_time,
                image_url: image_url
            })
        })
        .then(response => response.text())
        .then(response => {
            alert(response); // Show response message
            document.getElementById('itemName').value = '';
            document.getElementById('itemPrice').value = '';
            document.getElementById('itemDeliveryTime').value = '';
            document.getElementById('itemImage').value = '';
            fetchItems(); // Refresh items list
        });
    });

    // Delete item
    document.getElementById('deleteItemBtn').addEventListener('click', () => {
        const name = document.getElementById('itemToDelete').value;

        fetch('admin_dashboard.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'delete',
                name: name
            })
        })
        .then(response => response.text())
        .then(response => {
            alert(response); // Show response message
            document.getElementById('itemToDelete').value = '';
            fetchItems(); // Refresh items list
        });
    });

    // Fetch items
    function fetchItems() {
        fetch('admin_dashboard.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'fetch' })
        })
        .then(response => response.json())
        .then(items => {
            const container = document.getElementById('suggestionsContainer');
            container.innerHTML = '';
            items.forEach(item => {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'menu-item';
                itemDiv.innerHTML = `
                    <img src="${item.image_url}" alt="${item.name}">
                    <div>
                        <h3>${item.name}</h3>
                        <p>Price: ₹${item.price}</p>
                        <p>Delivery Time: ${item.delivery_time}</p>
                    </div>
                `;
                container.appendChild(itemDiv);
            });
        });
    }
</script>

</body>
</html>
