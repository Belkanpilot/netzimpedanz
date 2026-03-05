<?php
// WICHTIG: Session starten für den Sicherheits-Token
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration | Annual Conference 2026</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="programm.css">
</head>
<body>

<div id="header-placeholder" data-lang="en"></div>

<section class="section">
    <div class="container">

        <div class="registration-intro" style="margin-bottom: 20px;">
            <h1 style="color: var(--accent-color)"><strong>Registration April 23, 2026 - 8th Conference</strong></h1>
            <h2 class="reg-title">Rethinking Grid Utilization:<br>Storage & Overbuilding.</h2>
            <div class="reg-info-box">
                <div class="reg-info-col reg-info-ort">
                    <div class="venue-label">Venue</div>
                    <div class="venue-name">Ingenieurwerk Hamburg</div>
                    <div class="venue-address">
                        <a href="https://www.google.com/maps/search/?api=1&query=Georg-Wilhelm-Stra%C3%9Fe+187,+21107+Hamburg" target="_blank" rel="noopener noreferrer" class="venue-link">Georg-Wilhelm-Straße 187<br>21107 Hamburg<br>Germany</a>
                    </div>
                </div>
                <div class="reg-info-col reg-info-text">
                    <p class="reg-text" style="margin: 0;">
                        The energy transition poses new challenges. How do we stably integrate volatile generators into our grids? How do we maximize the use of existing infrastructure? On April 23, 2026, in Hamburg, we will dedicate ourselves to the key technologies of the coming decade.
                    </p>
                </div>
                <div class="reg-info-col reg-info-datum">
                    <div class="venue-label">Date & Time</div>
                    <div class="reg-datum">April 23, 2026</div>
                    <div class="reg-uhrzeit">09:00 – 17:00</div>
                </div>
            </div>
        </div>

        <div class="form-container" style="margin-bottom: 60px; border: 1px solid #e5e5e5;">

            <form action="register.php" method="POST">

                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <input type="hidden" name="_language" value="EN">

                <div style="display:none; opacity:0; visibility:hidden;">
                    <label for="website_hp">Website</label>
                    <input type="text" id="website_hp" name="website_hp" value="">
                </div>

                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" placeholder="John Doe" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" placeholder="john@example.com" required>
                </div>

                <div class="form-group">
                    <label for="company">Company / Institution *</label>
                    <input type="text" id="company" name="company" placeholder="Example Company Ltd." required>
                </div>

                <div class="form-group">
                    <label for="topic">Topic Proposal (Call for Presentations)</label>
                    <p style="font-size: 12px; color: #86868b; margin-bottom: 8px;">
                        Do you have an exciting contribution fitting our main topic? (Optional)
                    </p>
                    <textarea id="topic" name="topic" placeholder="Short description of your proposal..."></textarea>
                </div>

                <div class="form-group checkbox-group">
                    <input type="checkbox" id="privacy" name="privacy" required>
                    <label for="privacy" style="font-weight: 400; margin: 0;">
                        I agree that my data will be stored for the organization of the event.
                    </label>
                </div>

                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" class="btn" style="width: 100%; border: none; cursor: pointer;">Register Now (Binding)</button>
                </div>

            </form>
        </div>

        <div class="registration-intro">
            <div class="reg-highlights">
                <p class="highlight-intro">We cordially invite you to be part of this discussion. Look forward to:</p>
                <ul>
                    <li>
                        <span class="check-icon">✓</span>
                        <span><strong>Practical Insights:</strong> Learn firsthand how grid operators are already successfully using battery storage and overbuilding today.</span>
                    </li>
                    <li>
                        <span class="check-icon">✓</span>
                        <span><strong>Future Technologies:</strong> Deep dive into topics such as grid-forming and intelligent grid control.</span>
                    </li>
                    <li>
                        <span class="check-icon">✓</span>
                        <span><strong>Exclusive Network:</strong> Exchange ideas with leading engineers from research and industry.</span>
                    </li>
                </ul>
            </div>

            <p class="reg-cfp">
                <strong>Call for Participation:</strong> Help shape the program. We are looking for thought leaders with exciting practical examples related to our key topics. Submit your proposal directly via the form above.
            </p>
        </div>

    </div>
</section>

<div id="footer-placeholder" data-lang="en"></div>

<script src="main.js"></script>
</body>
</html>