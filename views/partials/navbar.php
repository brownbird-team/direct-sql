<nav class="navbar">
    <div class="logo-container">
        <a class="logo">BrownBird Team</a>
    </div>
    <ul class="nav-links">
        <li><a href="#">Home</a></li>
        <li><a href="#">About us</a></li>
        <li><a href="#">Database</a></li>
    </ul>
    <div class="burger" id="burger">
        <div class="line1"></div>
        <div class="line2"></div>
        <div class="line3"></div>
    </div>
</nav>
<script>
    const burger = document.querySelector('.burger');    
    const navLinks = document.querySelector('.nav-links');

    burger.addEventListener('click', () => {
        navLinks.classList.toggle('active');
        burger.classList.toggle('active');
    });
</script>