document.addEventListener('DOMContentLoaded', function() {
    const loginModal = document.getElementById("loginModal");
    const signInModal = document.getElementById("signInModal");
    const loginBtn = document.getElementById("loginBtn");
    const closeModalBtns = document.querySelectorAll(".close");
    const loginForm = document.getElementById("loginForm");
    const signInForm = document.getElementById("signInForm");
    const notification = document.getElementById("notification");
    const searchInput = document.getElementById("text");
    const itemsContainer = document.getElementById("suggestionsContainer");
    const images = document.querySelectorAll('.topimg img');
    let currentIndex = 0;

    function autoHover() {
        images.forEach((img, index) => img.classList.remove('hover-effect'));
        images[currentIndex].classList.add('hover-effect');
        currentIndex = (currentIndex + 1) % images.length;
    }

    setInterval(autoHover, 2000); // Change every 2 seconds

    let usersData = JSON.parse(localStorage.getItem("usersData")) || [];
    let isLoggedIn = localStorage.getItem("isLoggedIn") === "true";

    if (!isLoggedIn) {
        loginModal.style.display = "block";
    } else {
        loginBtn.innerHTML = '<i class="fa-sharp fa-solid fa-user"></i>Logout';
    }

    loginBtn.addEventListener("click", () => {
        if (isLoggedIn) {
            const confirmLogout = confirm("Are you sure you want to log out?");
            if (confirmLogout) {
                isLoggedIn = false;
                localStorage.setItem("isLoggedIn", "false");
                loginBtn.innerHTML = '<i class="fa-sharp fa-solid fa-user"></i>Log In'; 
                alert("You have been logged out.");
                location.reload();
            } else {
                return;
            }
        } else {
            loginModal.style.display = "block";
        }
    });

    document.getElementById('signUpGuest').addEventListener('click', function() {
        loginModal.style.display = "none";
        signInModal.style.display = "none";
        alert("You have successfully logged in as Guest.");
        isLoggedIn = true; 
        localStorage.setItem("isLoggedIn", "true"); 
        loginBtn.innerHTML = '<i class="fa-sharp fa-solid fa-user"></i>Logout'; 
    });

    loginForm.addEventListener("submit", (event) => {
        event.preventDefault();
        const username = loginForm.uname.value.trim();
        const password = loginForm.psw.value.trim();

        const foundUser = usersData.find(user => (user.username === username || user.email === username) && user.password === password);

        if (foundUser) {
            isLoggedIn = true;
            localStorage.setItem("isLoggedIn", "true");
            notification.style.display = "block";
            setTimeout(() => {
                notification.style.display = "none";
            }, 3000);
            loginModal.style.display = "none";
            loginBtn.innerHTML = '<i class="fa-sharp fa-solid fa-user"></i>Logout'; 
        } else {
            alert("Invalid username or password.");
        }
    });

    document.getElementById("goToSignIn").addEventListener("click", () => {
        loginModal.style.display = "none";
        signInModal.style.display = "block";
    });

    signInForm.addEventListener("submit", (event) => {
        event.preventDefault();
        const email = signInForm.email.value.trim();
        const username = signInForm.username.value.trim();
        const password = signInForm.password.value.trim();
        const confirmPassword = signInForm.confirmPassword.value.trim();

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

        const existingUser = usersData.find(user => user.username === username || user.email === email);

        if (existingUser) {
            alert("Username or email is already taken. Please choose another.");
            return;
        }

        if (emailPattern.test(email) && passwordPattern.test(password) && password === confirmPassword) {
            const newUser = { username, email, password };
            usersData.push(newUser);
            localStorage.setItem("usersData", JSON.stringify(usersData));
            alert("Account created successfully. You can now log in.");
            signInModal.style.display = "none";
            loginModal.style.display = "block"; 
        } else {
            alert("Please ensure all fields are valid and passwords match.");
        }
    });

    // Filter items on search
    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toLowerCase();
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(filter) ? "" : "none";
        });
    });

    // Initialize items from localStorage
    const storedItems = JSON.parse(localStorage.getItem("menuItems")) || [];
    storedItems.forEach(item => addItemToContainer(item));

    // Add item functionality
    document.getElementById('addItemBtn').addEventListener('click', addItem);

    function addItem() {
        const itemName = document.getElementById("itemName").value;
        const itemPrice = document.getElementById("itemPrice").value;
        const itemDeliveryTime = document.getElementById("itemDeliveryTime").value;
        const itemImage = document.getElementById("itemImage").value;

        if (!itemName || !itemPrice || !itemDeliveryTime || !itemImage) {
            alert("Please fill in all fields.");
            return;
        }

        const newItem = { name: itemName, price: itemPrice, deliveryTime: itemDeliveryTime, image: itemImage };
        storedItems.push(newItem);
        localStorage.setItem("menuItems", JSON.stringify(storedItems));
        addItemToContainer(newItem); // Add item to the container

        // Clear the input fields after adding the item
        document.getElementById("itemName").value = "";
        document.getElementById("itemPrice").value = "";
        document.getElementById("itemDeliveryTime").value = "";
        document.getElementById("itemImage").value = "";
    }

    function addItemToContainer(item) {
        const newItem = document.createElement("div");
        newItem.classList.add("suggest"); // Add class for styling
    
        newItem.innerHTML = `
            <a href="order.html">
                <img id="addimg" src="${item.image}" alt="${item.name}">
                <div class="divi">
                    <strong>${item.name}</strong><br>
                    <div class="details">
                        <span class="rating">
                            <i class="fa-sharp fa-solid fa-star-half-stroke"></i> 4.5
                        </span>
                        <span class="price">Price: ₹${item.price}</span>
                    </div>
                    <span class="delivery-time">Delivery: ${item.deliveryTime} mins</span>
                </div>
            </a>
        `;
    
        // Append new item to the suggestions container
        itemsContainer.appendChild(newItem);
    }
    
  
    


    // Existing event listeners...
document.getElementById('addItemBtn').addEventListener('click', addItem);

// New delete functionality
document.getElementById('deleteItemBtn').addEventListener('click', deleteItem);

function deleteItem() {
    const itemToDelete = document.getElementById("itemToDelete").value.trim();

    // Confirm the deletion
    const confirmDelete = confirm(`Are you sure you want to delete the item "${itemToDelete}"?`);

    if (confirmDelete) {
        // Find the index of the item to delete
        const index = storedItems.findIndex(item => item.name.toLowerCase() === itemToDelete.toLowerCase());

        if (index > -1) {
            // Remove the item from the storedItems array
            storedItems.splice(index, 1);
            localStorage.setItem("menuItems", JSON.stringify(storedItems)); // Update localStorage
            itemsContainer.innerHTML = ""; // Clear the container
            storedItems.forEach(item => addItemToContainer(item)); // Re-populate the container
            alert(`Item "${itemToDelete}" has been deleted.`);
        } else {
            alert(`Item "${itemToDelete}" not found.`);
        }
    } else {
        // If the user cancels the deletion, do nothing
        alert('Deletion canceled.');
    }

    // Clear the input field after deletion or cancellation
    document.getElementById("itemToDelete").value = "";
}


});
