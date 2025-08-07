<?php
session_start();
require_once '../classes/Room.php';
$room = new Room();
$rooms = $room->getAllRooms();

// Get available room types for display
$roomTypes = [
    [
        'id' => 101,
        'name' => 'Ocean View Room',
        'room_number' => 'Room 101',
        'price' => 180,
        'status' => 'Available',
        'description' => 'Wake up to breathtaking ocean views from your private balcony. This comfortable room features coastal-inspired décor and modern amenities for the perfect seaside getaway.',
        'amenities' => ['2 guests', '1 Ocean Bed', '35 sqm'],
        'features' => ['Free WiFi', 'Ocean View', 'Private Balcony'],
        'images' => 'Min Fridge'
    ],
    [
        'id' => 102,
        'name' => 'Ocean View Room',
        'room_number' => 'Room 102',
        'price' => 180,
        'status' => 'Currently Occupied',
        'description' => 'Wake up to breathtaking ocean views from your private balcony. This comfortable room features coastal-inspired décor and modern amenities for the perfect seaside getaway.',
        'amenities' => ['2 guests', '1 Ocean Bed', '35 sqm'],
        'features' => ['Free WiFi', 'Ocean View', 'Private Balcony'],
        'images' => 'Min Fridge'
    ],
    [
        'id' => 201,
        'name' => 'Deluxe Ocean Suite',
        'room_number' => 'Room 201',
        'price' => 280,
        'status' => 'Available',
        'description' => 'Experience luxury in our spacious deluxe suite with separate living area, premium furnishings, and panoramic ocean views from multiple windows.',
        'amenities' => ['4 guests', '1 King Bed + Sofa', '55 sqm'],
        'features' => ['Free WiFi', 'Ocean View', 'Living Area', 'Mini Bar'],
        'images' => 'Premium Amenities'
    ],
    [
        'id' => 301,
        'name' => 'Presidential Suite',
        'room_number' => 'Room 301',
        'price' => 450,
        'status' => 'Available',
        'description' => 'Ultimate luxury accommodation with master bedroom, separate living and dining areas, private terrace with jacuzzi, and unobstructed ocean views.',
        'amenities' => ['6 guests', 'Master Bedroom + Living', '85 sqm'],
        'features' => ['Free WiFi', 'Ocean View', 'Private Terrace', 'Jacuzzi', 'Butler Service'],
        'images' => 'Luxury Amenities'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oceanfront Accommodations - Ocean Breeze Resort</title>
    <link rel="stylesheet" href="modern_style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2>🌊 Ocean Breeze Resort</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="rooms.php" class="active">Rooms</a></li>
                <li><a href="reservation_form.php">Login</a></li>
                <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                    <li><a href="admin_dashboard.php">Admin</a></li>
                <?php endif; ?>
            </ul>
            <div class="nav-buttons">
                <?php if (isset($_SESSION['user'])): ?>
                    <span class="user-welcome">Welcome, <?= htmlspecialchars($_SESSION['user']['name']); ?></span>
                    <a href="logout.php" class="btn-secondary">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn-secondary">Login</a>
                    <a href="register.php" class="btn-primary">Book Now</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="rooms-header">
        <div class="container">
            <h1>Oceanfront Accommodations</h1>
            <p>Choose from our collection of luxurious oceanfront rooms and suites, each offering breathtaking views and direct access to our pristine private beach.</p>
        </div>
    </section>

    <!-- Booking Search Section -->
    <section class="booking-search">
        <div class="container">
            <div class="search-card">
                <h3>🔍 Find Your Perfect Ocean Retreat</h3>
                <p>Search available rooms for your special getaway</p>
                
                <form class="search-form" method="POST" action="check_availability.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="checkin">Check-in Date</label>
                            <input type="date" id="checkin" name="checkin" required>
                        </div>
                        <div class="form-group">
                            <label for="checkout">Check-out Date</label>
                            <input type="date" id="checkout" name="checkout" required>
                        </div>
                        <div class="form-group">
                            <label for="guests">Guests</label>
                            <select id="guests" name="guests" required>
                                <option value="1">1 Guest</option>
                                <option value="2" selected>2 Guests</option>
                                <option value="3">3 Guests</option>
                                <option value="4">4 Guests</option>
                                <option value="6">6 Guests</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn-search">Search Rooms</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Rooms Grid Section -->
    <section class="rooms-showcase">
        <div class="container">
            <div class="rooms-grid-modern">
                <?php foreach ($roomTypes as $roomType): ?>
                <div class="room-card-modern">
                    <div class="room-header-modern">
                        <div class="room-view-badge">🌊 Ocean View</div>
                        <h3><?= $roomType['name'] ?></h3>
                        <p class="room-number"><?= $roomType['room_number'] ?></p>
                    </div>
                    
                    <div class="room-content">
                        <div class="room-image-modern">
                            <div class="room-placeholder-modern"><?= $roomType['name'] ?></div>
                        </div>
                        
                        <div class="room-details-modern">
                            <div class="price-section">
                                <span class="price-large">$<?= $roomType['price'] ?></span>
                                <span class="price-period">per night</span>
                                <span class="availability <?= strtolower(str_replace(' ', '-', $roomType['status'])) ?>">
                                    <?= $roomType['status'] ?>
                                </span>
                            </div>
                            
                            <p class="room-description-modern"><?= $roomType['description'] ?></p>
                            
                            <div class="room-amenities">
                                <?php foreach ($roomType['amenities'] as $amenity): ?>
                                    <span class="amenity-tag">📋 <?= $amenity ?></span>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="room-features">
                                <?php foreach ($roomType['features'] as $feature): ?>
                                    <span class="feature-tag">✓ <?= $feature ?></span>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="room-extras">
                                <span class="extra-info">📷 <?= $roomType['images'] ?></span>
                                <span class="extra-info">ℹ️ 2 more</span>
                            </div>
                            
                            <div class="room-actions">
                                <?php if ($roomType['status'] === 'Available'): ?>
                                    <button class="btn-book-room" onclick="bookRoom(<?= $roomType['id'] ?>)">
                                        Book This Room
                                    </button>
                                <?php else: ?>
                                    <button class="btn-occupied" disabled>
                                        Currently Occupied
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Additional Info Section -->
    <section class="booking-info">
        <div class="container">
            <div class="info-grid">
                <div class="info-card">
                    <h4>🏖️ Beach Access</h4>
                    <p>All rooms include complimentary access to our private beach with premium amenities.</p>
                </div>
                <div class="info-card">
                    <h4>🍽️ Dining Included</h4>
                    <p>Enjoy breakfast and dinner at our oceanfront restaurant with your stay.</p>
                </div>
                <div class="info-card">
                    <h4>🚗 Free Services</h4>
                    <p>Complimentary WiFi, parking, and 24/7 concierge service for all guests.</p>
                </div>
                <div class="info-card">
                    <h4>💆‍♀️ Spa & Wellness</h4>
                    <p>Access to our luxury spa, fitness center, and infinity pool facilities.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Ocean Breeze Resort</h3>
                    <p>Creating unforgettable memories in paradise since 1995. Experience luxury, comfort, and exceptional service.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="rooms.php">Our Rooms</a></li>
                        <li><a href="reservation_form.php">Reservations</a></li>
                        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                            <li><a href="admin_dashboard.php">Admin Dashboard</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <ul>
                        <li>123 Ocean Drive, Paradise Island</li>
                        <li>Phone: +1 (555) 123-4567</li>
                        <li>Email: info@oceanbreezeresort.com</li>
                        <li>Reservations: reservations@oceanbreezeresort.com</li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Follow Us</h4>
                    <div class="social-links">
                        <a href="#" class="social-link">Facebook</a>
                        <a href="#" class="social-link">Instagram</a>
                        <a href="#" class="social-link">Twitter</a>
                        <a href="#" class="social-link">TripAdvisor</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Ocean Breeze Resort. All rights reserved. | Privacy Policy | Terms of Service</p>
            </div>
        </div>
    </footer>

    <script>
        function bookRoom(roomId) {
            // Get form data
            const checkin = document.getElementById('checkin').value;
            const checkout = document.getElementById('checkout').value;
            const guests = document.getElementById('guests').value;
            
            if (!checkin || !checkout) {
                alert('Please select check-in and check-out dates first.');
                return;
            }
            
            // Redirect to reservation form with room details
            window.location.href = `reservation_form.php?room_id=${roomId}&checkin=${checkin}&checkout=${checkout}&guests=${guests}`;
        }
        
        // Set minimum date to today
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('checkin').min = today;
            document.getElementById('checkout').min = today;
            
            // Update checkout min date when checkin changes
            document.getElementById('checkin').addEventListener('change', function() {
                document.getElementById('checkout').min = this.value;
            });
        });
    </script>
</body>
</html>
