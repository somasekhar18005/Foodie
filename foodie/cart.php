<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="./css/style.css">

    <style>
        body { font-family: Arial, sans-serif; background-color: #f3f3f3; }
        .cart { max-width: 400px; margin: 50px auto; padding: 20px; background-color: #fff; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); border-radius: 8px; }
        .cart h2 { text-align: center; color: #ff6600; }
        .cart-items { list-style-type: none; padding: 0; }
        .cart-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #ccc; }
        .cart-item img { width: 50px; height: 50px; border-radius: 4px; }
        .cart-item button { padding: 5px; background-color: #ff6600; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        .place-order { width: 100%; padding: 10px; background-color: #ff6600; color: #fff; border: none; border-radius: 4px; cursor: pointer; margin-top: 15px; }
    </style>
</head>
<body>
    <header class="header">
        <a href="home.php">
            <img id="swiggyimg" src="data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2024%2024%22%3E%3Cpath%20fill%3D%22%23f1600a%22%20d%3D%22M16.84%2C11.63A3%2C3%2C0%2C0%2C0%2C19%2C10.75l2.83-2.83a1%2C1%2C0%2C0%2C0%2C0-1.41%2C1%2C1%2C0%2C0%2C0-1.42%2C0L17.55%2C9.33a1%2C1%2C0%2C0%2C1-1.42%2C0h0L19.67%2C5.8a1%2C1%2C0%2C1%2C0-1.42-1.42L14.72%2C7.92a1%2C1%2C0%2C0%2C1%2C0-1.41l2.83-2.83a1%2C1%2C0%2C1%2C0-1.42-1.42L13.3%2C5.09a3%2C3%2C0%2C0%2C0%2C0%2C4.24h0L12%2C10.62%2C3.73%2C2.32l-.1-.06a.71.71%2C0%2C0%2C0-.17-.11l-.18-.07L3.16%2C2H3.09l-.2%2C0a.57.57%2C0%2C0%2C0-.18%2C0%2C.7.7%2C0%2C0%2C0-.17.09l-.16.1-.07%2C0-.06.1a1%2C1%2C0%2C0%2C0-.11.17%2C1.07%2C1.07%2C0%2C0%2C0-.07.19s0%2C.07%2C0%2C.11a11%2C11%2C0%2C0%2C0%2C3.11%2C9.34l2.64%2C2.63-5.41%2C5.4a1%2C1%2C0%2C0%2C0%2C0%2C1.42%2C1%2C1%2C0%2C0%2C0%2C.71.29%2C1%2C1%2C0%2C0%2C0%2C.71-.29L9.9%2C15.57h0l2.83-2.83h0l2-2A3%2C3%2C0%2C0%2C0%2C16.84%2C11.63ZM9.19%2C13.45%2C6.56%2C10.81A9.06%2C9.06%2C0%2C0%2C1%2C4%2C5.4L10.61%2C12Zm6.24.57A1%2C1%2C0%2C0%2C0%2C14%2C15.44l6.3%2C6.3A1%2C1%2C0%2C0%2C0%2C21%2C22a1%2C1%2C0%2C0%2C0%2C.71-.29%2C1%2C1%2C0%2C0%2C0%2C0-1.42Z%22%20class%3D%22color000000%20svgShape%22%2F%3E%3C%2Fsvg%3E">
        </a>
        <h1 id="h1title">ARITAKU<br><span>CANTEEN</span></h1>
        <ul class="nav-links">
        <li><a href="home.php"><i class="fa-solid fa-money-check-dollar"></i> HOME</a></li>
            <li><a href="prices.php"><i class="fa-solid fa-money-check-dollar"></i> PRICE</a></li>
            <li><a href="feedback.php"><i class="fa-solid fa-star"></i> FEEDBACK</a></li>
            <li><a href="about.php"><i class="fa-solid fa-circle-info"></i> ABOUT</a></li>
            <li><a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> CART</a></li>
        </ul>
        <div class="search-bar">
            <input type="text" id="text" placeholder="Search items">
        </div>
        <button id="loginBtn" class="login-button"><i class="fa-solid fa-user"></i> Log In</button>
        <a class="help" href="help.php">Help <i id="i1" class="fa-solid fa-question"></i></a>
    </header>

<div class="cart">
    <h2>Your Cart</h2>
    <ul class="cart-items" id="cart-items">
        <!-- Cart items dynamically rendered here -->
    </ul>
    <button class="place-order" onclick="placeOrder()">Place Order</button>
</div>

<script>
    // Function to render cart items
    function renderCart() {
        const cartItemsContainer = document.getElementById("cart-items");
        cartItemsContainer.innerHTML = ""; // Clear existing items
        const cart = JSON.parse(localStorage.getItem("cart")) || [];
        
        cart.forEach((item, index) => {
            const cartItem = document.createElement("li");
            cartItem.className = "cart-item";
            cartItem.innerHTML = `
                <img src="${item.img}" alt="${item.name}">
                <span>${item.name} - ${item.price}</span>
                <button onclick="removeFromCart(${index})">Remove</button>
            `;
            cartItemsContainer.appendChild(cartItem);
        });
    }

    // Function to remove item from cart
    function removeFromCart(index) {
        let cart = JSON.parse(localStorage.getItem("cart")) || [];
        cart.splice(index, 1); // Remove item at index
        localStorage.setItem("cart", JSON.stringify(cart));
        renderCart(); // Re-render cart
    }

    // Function to place the order
    function placeOrder() {
        const cart = JSON.parse(localStorage.getItem("cart")) || [];
        if (cart.length > 0) {
            alert("Order placed successfully!");
            localStorage.removeItem("cart"); // Clear the cart
            renderCart(); // Refresh the cart display
        } else {
            alert("Your cart is empty.");
        }
    }

    renderCart(); // Initial render of cart
</script>

</body>
</html>
