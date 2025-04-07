<?php
// check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // make sure email is filled in
    if (!empty($_POST["email"])) {
        $email = trim($_POST["email"]); // remove extra spaces

        // clean up the email input
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        // check if it's a valid email
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['newsletter_success'] = "thank you! your email has been added to our newsletter.";
        } else {
            $_SESSION['newsletter_error'] = "please enter a valid email address.";
        }
    } else {
        $_SESSION['newsletter_error'] = "email address is required.";
    }

    exit();
}
?>

<footer class="footer-container">
    <div class="footer">
        <div class="footer-column">
            <div class="footer-email-form">
                <h3>sign up for our newsletter</h3>
                <p>be the first to know about our special offers</p>
                <form id="newsletter-form" action="includes/footer.php" method="post">
                    <div class="input-container">
                        <input id="email" type="email" name="email" placeholder="email address" required>
                        <button type="submit">sign up</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="footer-column">
            <h4>buying</h4>
            <ul>
                <a href="accountDetails.php">my account</a>
                <a href="myOrders.php">my orders</a>
            </ul>
            <h4>selling</h4>
            <ul>
                <a href="sell.php">start selling</a>
                <a href="musicOnline.php">selling faqs</a>
            </ul>
        </div>

        <div class="footer-column">
            <h4>about</h4>
            <ul>
                <a href="musicOnline.php">about us</a>
                <a href="musicOnline.php">buyer policy</a>
                <a href="musicOnline.php">seller policy</a>
                <a href="musicOnline.php">privacy policy</a>
                <a href="musicOnline.php">terms of use</a>
            </ul>
        </div>

        <div class="footer-column">
            <h4>support</h4>
            <ul>
                <a href="musicOnline.php">help centre</a>
                <a href="musicOnline.php">contact us</a>
            </ul>

            <h4>social</h4>
            <div class="social-links">
                <a href="www.facebook.com"><i class="fa-brands fa-facebook"></i></a>
                <a href="www.instagram.com"><i class="fa-brands fa-instagram"></i></a>
                <a href="x.com"><i class="fa-brands fa-twitter"></i></a>
            </div>
        </div>
    </div>
</footer>
