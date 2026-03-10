<nav class="navbar">
    <div class="nav-container">
        <a href="#" class="logo">Milk<span>Coffee</span></a>

        <ul class="nav-links" id="navLinks">
            <li><a href="#">Home</a></li>
            <li><a href="#">Courses</a></li>
            <li><a href="#">Projects</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#" class="btn">Login</a></li>
        </ul>

        <div class="menu-toggle" id="menuToggle">
            ☰
        </div>
    </div>
</nav>
<style>
    /* Reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Segoe UI", system-ui, sans-serif;
    }

    /* Navbar */
    .navbar {
        background: #0f172a;
        /* dark slate */
        padding: 0.8rem 1.5rem;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    /* Container */
    .nav-container {
        max-width: 1200px;
        margin: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Logo */
    .logo {
        color: #fff;
        font-size: 1.4rem;
        font-weight: 700;
        text-decoration: none;
    }

    .logo span {
        color: #38bdf8;
    }

    /* Links */
    .nav-links {
        list-style: none;
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .nav-links a {
        color: #e5e7eb;
        text-decoration: none;
        font-size: 0.95rem;
        transition: color 0.3s ease;
    }

    .nav-links a:hover {
        color: #38bdf8;
    }

    /* Button link */
    .nav-links .btn {
        padding: 0.4rem 1rem;
        border-radius: 20px;
        background: #38bdf8;
        color: #0f172a;
        font-weight: 600;
    }

    .nav-links .btn:hover {
        background: #0ea5e9;
        color: #020617;
    }

    /* Mobile menu icon */
    .menu-toggle {
        display: none;
        font-size: 1.8rem;
        color: #fff;
        cursor: pointer;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .menu-toggle {
            display: block;
        }

        .nav-links {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: #020617;
            flex-direction: column;
            gap: 1rem;
            padding: 1rem 0;
            display: none;
        }

        .nav-links.active {
            display: flex;
        }
    }
</style>
<script>
    const menuToggle = document.getElementById("menuToggle");
    const navLinks = document.getElementById("navLinks");

    menuToggle.addEventListener("click", () => {
        navLinks.classList.toggle("active");
    });

</script>