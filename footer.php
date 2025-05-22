<?php
require_once 'connections.php';

// Get site settings
$settings = [];
$result = $conn->query("SELECT setting_name, setting_value FROM site_settings");
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_name']] = $row['setting_value'];
}
?>

<footer>
  <div class="footer-container">
    <!-- Store Hours -->
    <div class="footer-box">
      <h3 class="footer-title">Store Hours</h3>
      <ul class="footer-list">
        <li><i class="fas fa-clock"></i> <?php echo htmlspecialchars($settings['store_hours_weekdays']); ?></li>
        <li><i class="fas fa-clock"></i> <?php echo htmlspecialchars($settings['store_hours_weekend']); ?></li>
      </ul>
    </div>
    <!-- Contact Details -->
    <div class="footer-box">
      <h3 class="footer-title">Contact Us</h3>
      <ul class="footer-list">
        <li><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($settings['store_address']); ?></li>
        <li><i class="fas fa-phone"></i> <?php echo htmlspecialchars($settings['store_phone']); ?></li>
        <li><i class="fas fa-envelope"></i> <a href="mailto:<?php echo htmlspecialchars($settings['store_email']); ?>"><?php echo htmlspecialchars($settings['store_email']); ?></a></li>
      </ul>
    </div>
    <!-- Follow & About -->
    <div class="footer-box">
      <h3 class="footer-title">Follow Me</h3>
      <div class="social-icons">
        <a href="<?php echo htmlspecialchars($settings['facebook_url']); ?>" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
        <a href="<?php echo htmlspecialchars($settings['instagram_url']); ?>" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
      </div>
      <h3 class="footer-title mt-4"><a href="<?php echo htmlspecialchars($settings['about_page_url']); ?>" class="about-link">About Me!</a></h3>
    </div>
  </div>
  <div class="footer-bottom">
    <p>&copy; <?= date('Y') ?> <?php echo htmlspecialchars($settings['copyright_text']); ?></p>
  </div>
</footer>

<!-- Include Font Awesome CDN for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
  :root {
    --primary-color: #4CAF50;
    --secondary-color: #333;
    --white: #fff;
    --light-gray: #f4f4f4;
    --font-family: 'Arial', sans-serif;
  }

  footer {
    background-color: var(--secondary-color);
    color: var(--white);
    padding: 40px 20px 20px;
    font-family: var(--font-family);
  }

  .footer-container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
  }

  .footer-box {
    display: flex;
    flex-direction: column;
  }

  .footer-title {
    font-size: 20px;
    margin-bottom: 15px;
    color: var(--primary-color);
    font-weight: bold;
  }

  .footer-list {
    list-style: none;
    padding: 0;
  }

  .footer-list li {
    margin-bottom: 10px;
    font-size: 14px;
    display: flex;
    align-items: center;
  }

  .footer-list li i {
    margin-right: 10px;
    font-size: 16px;
    color: var(--primary-color);
  }

  .footer-list li a {
    color: var(--white);
    text-decoration: none;
  }

  .footer-list li a:hover {
    text-decoration: underline;
  }

  .social-icons {
    display: flex;
    gap: 15px;
    margin-top: 10px;
  }

  .social-icons a {
    display: inline-block;
    width: 40px;
    height: 40px;
    background-color: var(--primary-color);
    color: var(--white);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s;
  }

  .social-icons a:hover {
    background-color: #388E3C;
  }

  .about-link {
    display: inline-block;
    margin-top: 15px;
    font-size: 14px;
    color: var(--white);
    text-decoration: underline;
    transition: color 0.3s;
  }

  .about-link:hover {
    color: #ccc;
  }

  .footer-bottom {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid #444;
    font-size: 13px;
    color: #ccc;
  }

  @media(max-width: 768px) {
    .footer-container {
      grid-template-columns: 1fr;
      gap: 20px;
    }
  }
</style>