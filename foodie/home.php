<?php
// Connect to MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "canteen_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle incoming POST requests
$action = $_POST['action'] ?? '';

if ($action === 'add') {
    // Add a new item
    $name = $_POST['name'];
    $price = $_POST['price'];
    $delivery_time = $_POST['delivery_time'];
    $image_url = $_POST['image_url'];

    $stmt = $conn->prepare("INSERT INTO items (name, price, delivery_time, image_url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $name, $price, $delivery_time, $image_url);

    if ($stmt->execute()) {
        echo "Item added successfully!";
    } else {
        echo "Error adding item: " . $stmt->error;
    }

    $stmt->close();
    exit;
} elseif ($action === 'delete') {
    // Delete an item
    $name = $_POST['name'];

    $stmt = $conn->prepare("DELETE FROM items WHERE name = ?");
    $stmt->bind_param("s", $name);

    if ($stmt->execute()) {
        echo "Item deleted successfully!";
    } else {
        echo "Error deleting item: " . $stmt->error;
    }

    $stmt->close();
    exit;
} elseif ($action === 'fetch') {
    // Fetch all items
    $result = $conn->query("SELECT * FROM items");

    $items = array();
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    echo json_encode($items);
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARITAKU Canteen Menu</title>
    <style>
        /* Basic styling */
        .items-containers1, .add-item-form {
            margin: 20px;
            padding: 10px;
        }
        .menu-item {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .item-image {
            width: 100px;
            height: auto;
        }
        .add-item-form input, .delete-item-form input, .add-item-form button, .delete-item-form button {
            margin: 5px;
        }
        body {
    background-color:rgba(255, 166, 0, 0.848);
    background-size: cover;
    background-position: center;
    height: 100vh;
    margin: 0;
    font-family: Arial, sans-serif;
}
.topimg {
    width: 70%;
    height: 450px;
    display: flex;
    margin-left: 220px;
    align-content: center;
    align-items: center;
    justify-content: center;
    gap: 10px;
}
.header {
    position: fixed;
    top: 0;
    width: 100%;
    background-color: #f8f9fa;
    z-index: 10;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); 
}

#img11{
    width: 10%;
    height: 60%;
    filter:contrast(100%);
    object-fit: cover;
    border-radius: 24px;
    transition: all ease-in-out 0.5s;
}
#img11.hover-effect {
    width: 70%;
    height: 60%;
    object-fit: cover;
    border-radius: 24px;
    border: 2px solid rgb(133, 128, 128);
    transition: all ease-in-out 0.5s;
}

.header {
    display: flex;
    align-items: center;
    padding: 10px;
    width:100%;
    position:fixed;
    background-color: rgba(0, 0, 0, 0.994);
    
}

.header ul{
    display: flex;
    align-items: center;
    list-style: none;
    padding: -30px;
    margin-right:40px;
}
h1 {
    font-style: oblique;
    font-size: 25px;
    color:white;
    margin-right: 50px;
}
h1 span {
    color: red;
}
li {
    margin: 0 15px;
    background-color:white;
    border-radius:20px;
    border:10px solid white;
    font-size:17px;
}
li:hover {
    border-radius: 20px;
}
a {
    color: black;
    font-weight: bold;
    text-decoration: none;
}
a:hover {
    color: red;
}
i {
    margin-right: 8px;
}
.search-bar {
    display: flex;
    align-items: center;
    margin-right: 40px;
}
.search-bar input {
    padding: 10px;
    font-size: 16px;
    border-radius: 20px;
    border: none;
    outline: none;
}
.login-button {
    font-size: 20px;
    background-color: green;
    border-radius: 20px;
    padding: 10px 20px;
    color: white;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    margin-right: 40px;
}
.login-button i {
    color: white;
    margin-right: 8px;
}
.login-button:hover {
    background-color: rgba(21, 245, 13, 0.248);
}
.label1{
    color:white;
}
#i1{
    color:white;
}
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.911);
    padding: 60px 0;
    overflow: auto;
    z-index: 1000;
}
.modal-content {
    background: #fff;
    margin: auto;
    padding: 20px;
    border-radius: 10px;
    max-width: 400px;
    width: 80%;
    text-align: center;
}
.modal-content h2 {
    margin-top: 0;
}
.modal input,
.modal button {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border-radius: 5px;
    box-sizing: border-box;
}
.modal input {
    border: 1px solid #ccc;
    font-size: 20px;
}
.modal button {
    background: red;
    color: #fff;
    border: none;
    cursor: pointer;
    font-size: 18px;
}
.modal button:hover {
    background: green;
}
.notification {
    display: none;
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: #4caf50;
    color: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    font-size: 24px;
    text-align: center;
}
.items-container {
    display: grid;
    flex-direction: column;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    justify-content: space-between;
    background-color: rgba(255, 255, 255, 0.8);
    margin-top: 20px;
    border-radius: 20px;
}
.items-container a {
    text-align: center;
    margin: 20px;
    text-decoration: none;
    color: black;
    font-size:20px;
}
.items-container img {
    width: 250px;
    height: 200px;
    filter:contrast(130%);
    border-radius: 20px;
}
img:hover{
    width:245px;
    height:195px;
}
a:hover{
    color:red;
}
#swiggyimg{
    width:60px;
    height:60px;
    border-radius:50px;
}
/* Modal Content */
.modal-content {
    background-color:rgba(8, 8, 8, 0.861);
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    border-radius: 10px;
}
.modal-content input{
    margin-top: 15px;
    margin-bottom: 15px;
}
form {
    display: flex;
    flex-direction: column;
}
#h1sug{
    color:red;
    margin-left:20px;
    font-size: 30px;
}

#list{
    color:red;
    font-size: 30px;
    margin-left:20px;
    margin-top: -100px;
}
.marquee-container {
    display: flex;
    justify-content: center; /* Center horizontally */
    align-items: center; /* Center vertically (if height is set) */
    background-color: #fff3e0; /* Light background color */
    padding: 10px;
    border-radius: 5px; /* Rounded corners */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
}

marquee {
    font-size: 18px;
    color: #FF5722; /* SWIGGY orange */
    font-weight: bold;
}
.lastdiv img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
span{
    color:gold;
}
.help{
color:white;
}
h2{
    color:red;
    font-size: 38px;
    font-weight: bold;
}
/* Style for the modal close button */
.modal-content .close {
    display:flex;
    font-size: 24px;
    font-weight: bold;
    position: right;
    color: #fffefe;
    cursor: pointer;
    
}

.modal .close:hover {
    color: #f00; /* Change color on hover */
}


.divi {
    font-family: Arial, sans-serif;
    color: #060606f5;
    margin-left: 20px;
    align-items: center;
}
.details {
    display: flex;
    flex-direction: row;
    margin-top: 5px;
}

.rating {
    color:green;
    font-size: 17px;
    margin-right:40px;
}

.price {
    font-weight: bold;
    color: #d9534f;
    font-size: 18px;
}

.delivery-time {
    font-size: 17px;
    color: #d91fba;
    margin-top: 5px;
}
.items-containers1 {
    display: grid;                      /* Use grid layout for suggestions */
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* Responsive columns */
    gap: 20px;                          /* Space between suggestion items */
    margin: 20px;                       /* Margin around the suggestions container */
}
.items-containers1 img {
    width: 250px;                       /* Set width to 250px */
    height: 200px;                      /* Set height to 200px */
    filter: contrast(130%);             /* Increase contrast by 130% */
    border-radius: 20px;                /* Apply rounded corners */
    object-fit: cover;                  /* Ensure images cover the area without distortion */
    margin: 0 auto;                     /* Center the image in its grid cell */
}
/* General Styles */

/* Combined Item Form Styles */
.item-form {
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    width: 300px; /* Set width to 300px */
    height: 500px; /* Set height to 500px */
    display: flex;
    flex-direction: column;
}

/* Title Styles */
.item-form h2 {
    text-align: center;
    margin-bottom: 20px;
}

/* Add Item Form Styles */
.add-item-form,
.delete-item-form {
    margin-bottom: 15px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.add-item-form h3,
.delete-item-form h3 {
    margin: 0 0 10px;
    font-size: 16px;
    text-align: center;
}

/* Input Styles */
.add-item-form input[type="text"],
.delete-item-form input[type="text"] {
    width: calc(100% - 20px);
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-bottom: 10px;
    transition: border-color 0.3s;
}

.add-item-form input[type="text"]:focus,
.delete-item-form input[type="text"]:focus {
    border-color: #007bff;
    outline: none;
}

/* Button Styles */
.add-item-form button,
.delete-item-form button {
    width: 100%;
    background-color: #007bff;
    color: #ffffff;
    border: none;
    border-radius: 4px;
    padding: 10px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
}

.add-item-form button:hover,
.delete-item-form button:hover {
    background-color: #0056b3;
}

/* Responsive Styles */
@media (max-width: 600px) {
    .item-form {
        padding: 15px;
    }
}

.suggest{
    display: grid;
    flex-direction: column;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    justify-content: space-between;
    margin-top: 20px;
    border-radius: 20px;
}
.suggest img {
    width: 250px;
    height: 200px;
    border-radius: 20px;
    margin-top: 20px;
}
.suggest a{
    font-size:18px;
    text-align:center;
}
    </style>
</head>
<body>
<header class="header">
        <a href src="selecters.html"><img id="swiggyimg" src="data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2024%2024%22%3E%3Cpath%20fill%3D%22%23f1600a%22%20d%3D%22M16.84%2C11.63A3%2C3%2C0%2C0%2C0%2C19%2C10.75l2.83-2.83a1%2C1%2C0%2C0%2C0%2C0-1.41%2C1%2C1%2C0%2C0%2C0-1.42%2C0L17.55%2C9.33a1%2C1%2C0%2C0%2C1-1.42%2C0h0L19.67%2C5.8a1%2C1%2C0%2C1%2C0-1.42-1.42L14.72%2C7.92a1%2C1%2C0%2C0%2C1%2C0-1.41l2.83-2.83a1%2C1%2C0%2C1%2C0-1.42-1.42L13.3%2C5.09a3%2C3%2C0%2C0%2C0%2C0%2C4.24h0L12%2C10.62%2C3.73%2C2.32l-.1-.06a.71.71%2C0%2C0%2C0-.17-.11l-.18-.07L3.16%2C2H3.09l-.2%2C0a.57.57%2C0%2C0%2C0-.18%2C0%2C.7.7%2C0%2C0%2C0-.17.09l-.16.1-.07%2C0-.06.1a1%2C1%2C0%2C0%2C0-.11.17%2C1.07%2C1.07%2C0%2C0%2C0-.07.19s0%2C.07%2C0%2C.11a11%2C11%2C0%2C0%2C0%2C3.11%2C9.34l2.64%2C2.63-5.41%2C5.4a1%2C1%2C0%2C0%2C0%2C0%2C1.42%2C1%2C1%2C0%2C0%2C0%2C.71.29%2C1%2C1%2C0%2C0%2C0%2C.71-.29L9.9%2C15.57h0l2.83-2.83h0l2-2A3%2C3%2C0%2C0%2C0%2C16.84%2C11.63ZM9.19%2C13.45%2C6.56%2C10.81A9.06%2C9.06%2C0%2C0%2C1%2C4%2C5.4L10.61%2C12Zm6.24.57A1%2C1%2C0%2C0%2C0%2C14%2C15.44l6.3%2C6.3A1%2C1%2C0%2C0%2C0%2C21%2C22a1%2C1%2C0%2C0%2C0%2C.71-.29%2C1%2C1%2C0%2C0%2C0%2C0-1.42Z%22%20class%3D%22color000000%20svgShape%22%2F%3E%3C%2Fsvg%3E"></a>
        <h1 id="h1title">ARITAKU<br><span> CANTEEN</span></h1>
        <ul>
        <li><a href="home.php"><i class="fa-solid fa-money-check-dollar"></i> HOME</a></li>
            <li><a href="prices.html"><i class="fa-sharp fa-solid fa-money-check-dollar"></i>PRICE</a></li>
            <li><a href="feedback.html"><i class="fa-sharp fa-solid fa-star"></i>FEEDBACK</a></li>
            <li><a href="about.html"><i class="fa-duotone fa-solid fa-circle-info"></i>ABOUT</a></li>
            <li><a href="cart.php"><i class="fa-sharp fa-solid fa-cart-shopping"></i>CART</a></li>
        </ul>
        <div class="search-bar">
            <input type="text" id="text" placeholder="Search items">
        </div>
        <button id="loginBtn" class="login-button"><i class="fa-sharp fa-solid fa-user"></i><a href="index.php">Log out</a></button>
        <a class="help" href="help.html">Help<i id="i1" class="fa-sharp-duotone fa-solid fa-question"></i></a>
    </header>
    <div class="marquee-container">
        <marquee width="90%" direction="left" height="20px">
            🍔 Welcome to <span>ARITAKU CANTEEN!</span> Your favorite meals are just a click away! 🚀 Enjoy delicious food delivered to you in minutes!
        </marquee>
    </div>
    

    <!-- Notification Message -->
    <div id="notification" class="notification">
        <p>You are successfully logged in!</p>
    </div>
    <br><br>
    <div class="topimg">
        <img id="img11" src="https://tse1.mm.bing.net/th?id=OIP.a_2KQxjytt3-LGh5KcQwbgHaDq&pid=Api&P=0&h=180">
        <img id="img11" src="https://tse1.mm.bing.net/th?id=OIP.vy6EgOSG4cGaAKCNZCwcyAHaEc&pid=Api&P=0&h=180">
        <img id="img11" src="https://tse2.mm.bing.net/th?id=OIP.sLN12lPogfD9_QuYwmCV5AHaEm&pid=Api&P=0&h=180">
        <img id="img11" src="https://tse1.mm.bing.net/th?id=OIP.LJSWuEOsUS99hRudvhKA9QHaEU&pid=Api&P=0&h=180">
        <img id="img11" src="https://tse1.mm.bing.net/th?id=OIP.CA-63Nz_LsD_qVHw7VHCMwHaE7&pid=Api&P=0&h=180">
        <img id="img11" src="https://tse2.mm.bing.net/th?id=OIP.AQKJOhCV1mZx84niHI_SnwHaD4&pid=Api&P=0&h=180">
        <img id="img11" src="https://tse2.mm.bing.net/th?id=OIP.HozTQyBJ2_QL2lP0LCC1TwHaEF&pid=Api&P=0&h=180">
        <img id="img11" src="https://tse4.mm.bing.net/th?id=OIP.k2BBiVH5EIUJgZIWVlPC8wHaE8&pid=Api&P=0&h=180">
    </div> <br><br>
    <h1 id="list">Lists:</h1>
    <!-- Items Section -->
    <div class="items-container">
        <a href="bakery.php">
            <img src="https://tse1.mm.bing.net/th?id=OIP.MBum2nlNKS6QUfmEAm9tXgHaE7&pid=Api&P=0&h=180" alt="BAKERY-ITEMS">
            <p>BAKERY-ITEMS <i class="fa-sharp fa-solid fa-cake-candles"></i></p>
        </a>
        <a href="veg.php">
            <img src="https://tse1.mm.bing.net/th?id=OIP.aE3mmRnS1fF1W3nbpLNABwHaHa&pid=Api&rs=1&c=1&qlt=95&w=105&h=105" alt="VEG-FOODS">
            <p>VEG-ITEMS <i class="fa-sharp fa-solid fa-leaf"></i></p>
        </a>
        <a href="nonveg.php">
            <img src="https://tse1.mm.bing.net/th?id=OIP.kWTjKTUdRR2kzPfc3aDl5QHaGf&pid=Api&P=0&h=180" alt="NON-VEG-FOODS">
            <p>NON-VEG-ITEMS <i class="fa-sharp fa-solid fa-drumstick-bite"></i></p>
        </a>
        <a href="juice.php">
            <img src="https://tse1.mm.bing.net/th?id=OIP.kXGo8Cmpo_uDYFu4lUZcTgHaE8&pid=Api&rs=1&c=1&qlt=95&w=177&h=118" alt="JUICE-ITEMS">
            <p>JUICE-ITEMS <i class="fa-sharp fa-solid fa-wine-glass"></i></p>
        </a>
    </div>
    <h1 id="h1sug">Suggestions:</h1>
      <div class="suggest">
        
<a href="order.html">
    <img src="https://tse3.mm.bing.net/th?id=OIP.jfUjaGUYuFtfXcyDO5uaYAHaE8&pid=Api&P=0&h=180" alt="Ginger Biscuit">
    <div class="divi">
        <strong>Ginger Biscuit</strong><br>
        <div class="details">
            <span class="rating">
                <i class="fa-sharp fa-solid fa-star-half-stroke"></i> 4.0
            </span>
            <span class="price">Price: ₹70</span>
        </div>
        <span class="delivery-time">Delivery: 15 - 25 mins</span>
    </div>
</a>

    <a href="order.html">
    <img src="https://tse4.mm.bing.net/th?id=OIP.DYyoNQQz5t4d7VyLE-3RAwHaHa&pid=Api&P=0&h=180" alt="Choco Chip Biscuit">
    <div class="divi">
        <strong>Choco Chip Biscuit</strong><br>
        <div class="details">
            <span class="rating">
                <i class="fa-sharp fa-solid fa-star-half-stroke"></i> 4.4
            </span>
            <span class="price">Price: ₹90</span>
        </div>
        <span class="delivery-time">Delivery: 15 - 25 mins</span>
    </div>
</a>

<a href="order.html">
    <img src="https://tse2.mm.bing.net/th?id=OIP.vOsqHb_WBNSkFG87sOkcVwHaDt&pid=Api&P=0&h=180" alt="Pineapple Biscuit">
    <div class="divi">
        <strong>Pineapple Biscuit</strong><br>
        <div class="details">
            <span class="rating">
                <i class="fa-sharp fa-solid fa-star-half-stroke"></i> 4.3
            </span>
            <span class="price">Price: ₹95</span>
        </div>
        <span class="delivery-time">Delivery: 15 - 25 mins</span>
    </div>
</a>
<a href="order.html">
    <img src="https://tse4.mm.bing.net/th?id=OIP.1FfvT3fqdb96yvsl6Vqk9QHaEi&pid=Api&P=0&h=180" alt="Cashew Biscuit">
    <div class="divi">
        <strong>Cashew Biscuit</strong><br>
        <div class="details">
            <span class="rating">
                <i class="fa-sharp fa-solid fa-star-half-stroke"></i> 4.5
            </span>
            <span class="price">Price: ₹100</span>
        </div>
        <span class="delivery-time">Delivery: 15 - 25 mins</span>
    </div>
</a>
         
</div>
<div class="items-container" id="itemsContainer"></div>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const itemsContainer = document.getElementById('itemsContainer');
    const addItemBtn = document.getElementById('addItemBtn');
    const notification = document.getElementById('notification');
    const deleteModal = document.getElementById('deleteModal');
    const closeModal = document.getElementById('closeModal');
    const deleteItemBtn = document.getElementById('deleteItemBtn');
    const deleteNameInput = document.getElementById('deleteName');

    // Function to fetch and display items
    function fetchItems() {
        fetch('index.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=fetch'
        })
        .then(response => response.json())
        .then(data => {
            itemsContainer.innerHTML = '';
            data.forEach(item => {
                const itemDiv = document.createElement('div');
                itemDiv.classList.add('menu-item');
                itemDiv.innerHTML = `
                    <img src="${item.image_url}" alt="${item.name}" class="item-image">
                    <div>
                        <h3>${item.name}</h3>
                        <p class="price">Price: $${item.price.toFixed(2)}</p>
                        <p class="delivery-time">Delivery Time: ${item.delivery_time}</p>
                    </div>
                `;
                itemsContainer.appendChild(itemDiv);
            });
        })
        .catch(err => console.error(err));
    }

    // Function to show notification
    function showNotification(message) {
        notification.textContent = message;
        notification.style.display = 'block';
        setTimeout(() => {
            notification.style.display = 'none';
        }, 3000);
    }

    // Event listener for adding item
    addItemBtn.addEventListener('click', () => {
        const name = document.getElementById('name').value;
        const price = document.getElementById('price').value;
        const delivery_time = document.getElementById('delivery_time').value;
        const image_url = document.getElementById('image_url').value;

        if (!name || !price || !delivery_time || !image_url) {
            showNotification('Please fill in all fields!');
            return;
        }

        fetch('index.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=add&name=${name}&price=${price}&delivery_time=${delivery_time}&image_url=${image_url}`
        })
        .then(response => response.text())
        .then(data => {
            showNotification(data);
            fetchItems();
        })
        .catch(err => console.error(err));
    });

    // Event listener for delete button
    closeModal.addEventListener('click', () => {
        deleteModal.style.display = 'none';
    });

    // Event listener for delete item button
    deleteItemBtn.addEventListener('click', () => {
        const name = deleteNameInput.value;

        if (!name) {
            showNotification('Please enter an item name!');
            return;
        }

        fetch('index.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=delete&name=${name}`
        })
        .then(response => response.text())
        .then(data => {
            showNotification(data);
            fetchItems();
            deleteModal.style.display = 'none';
        })
        .catch(err => console.error(err));
    });

    // Initial fetch of items
    fetchItems();
});
</script>
</body>
</html>
