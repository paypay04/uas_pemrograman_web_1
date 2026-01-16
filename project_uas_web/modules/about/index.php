<?php
// modules/about/index.php
require_once __DIR__ . '/../../config.php';

$pageTitle = 'About Us - Harum Bakery';
include __DIR__ . '/../../views/header.php';
?>

<div class="container py-5">
    <!-- Hero Section -->
    <div class="row align-items-center mb-5">
        <div class="col-lg-6">
            <h1 class="display-4 fw-bold text-dark mb-3">
                About <span class="text-primary">Harum Bakery</span>
            </h1>
            <p class="lead text-muted mb-4">
                Sejak 2024, kami berkomitmen menyajikan produk bakery terbaik 
                dengan bahan-bahan pilihan dan rasa yang tak terlupakan.
            </p>
            <div class="d-flex gap-3">
                <a href="#contact-admin" class="btn btn-primary btn-lg px-4">
                    <i class="fas fa-headset me-2"></i>Contact Admin
                </a>
            <a href="<?php echo BASE_URL; ?>/products" class="btn btn-outline-primary btn-lg px-4">
                <i class="fas fa-shopping-bag me-2"></i>Shop Now
            </a>
            </div>
        </div>
        <div class="col-lg-6">
            <img src="<?php echo BASE_URL; ?>/assets/gambar/cake.jpg" 
                 alt="Harum Bakery Store" 
                 class="img-fluid rounded shadow-lg"
                 style="max-height: 400px; object-fit: cover;">
        </div>
    </div>

    <!-- Our Story -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <h2 class="h1 fw-bold text-dark text-center mb-4">
                        <i class="fas fa-book-open text-primary me-2"></i>Our Story
                    </h2>
                    
                    <div class="row g-4">
                        <!-- Timeline -->
                        <div class="col-md-4">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-date">2024</div>
                                    <div class="timeline-content">
                                        <h5>Berawal dari Hobi</h5>
                                        <p>Dimulai dari dapur rumah dengan passion untuk baking.</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-date">2025</div>
                                    <div class="timeline-content">
                                        <h5>E-commerce Launch</h5>
                                        <p>Meluncurkan website untuk menjangkau lebih banyak pelanggan.</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-date">Sekarang</div>
                                    <div class="timeline-content">
                                        <h5>Tumbuh Bersama</h5>
                                        <p>Melayani ratusan pelanggan dengan berbagai produk berkualitas.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Story Content -->
                        <div class="col-md-8">
                            <div class="story-content">
                                <h3 class="h2 text-dark mb-3">Misi Kami</h3>
                                <p class="text-muted mb-4">
                                    Harum Bakery didirikan dengan satu tujuan: 
                                    menyebarkan kebahagiaan melalui kue dan roti yang lezat. 
                                    Setiap produk yang kami buat adalah perpaduan sempurna 
                                    antara resep tradisional dan inovasi modern.
                                </p>
                                
                                <h3 class="h2 text-dark mb-3">Visi</h3>
                                <p class="text-muted mb-4">
                                    Menjadi toko bakery online terdepan di Indonesia yang 
                                    dikenal dengan kualitas, kreativitas, dan pelayanan terbaik.
                                </p>
                                
                                <div class="row mt-4">
                                    <div class="col-md-4 mb-3">
                                        <div class="text-center p-3">
                                            <i class="fas fa-award fa-2x text-primary mb-3"></i>
                                            <h5>Quality First</h5>
                                            <small class="text-muted">Bahan premium & fresh</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="text-center p-3">
                                            <i class="fas fa-heart fa-2x text-primary mb-3"></i>
                                            <h5>Made with Love</h5>
                                            <small class="text-muted">Dibuat dengan perhatian khusus</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="text-center p-3">
                                            <i class="fas fa-shipping-fast fa-2x text-primary mb-3"></i>
                                            <h5>Fast Delivery</h5>
                                            <small class="text-muted">Pengiriman cepat & aman</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Admin Section -->
    <div id="contact-admin" class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body p-5">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h2 class="h1 fw-bold mb-3">Butuh Bantuan?</h2>
                            <p class="lead mb-4">
                                Tim admin kami siap membantu Anda 24/7. 
                                Tanyakan apapun tentang produk, pemesanan, atau keluhan Anda.
                            </p>
                            <div class="d-flex flex-wrap gap-3">
                                <?php if (isLoggedIn()): ?>
                                    <?php if (isAdmin()): ?>
                                        <a href="<?php echo BASE_URL; ?>/dashboard" 
                                           class="btn btn-light btn-lg px-4">
                                            <i class="fas fa-cog me-2"></i>Go to Admin Panel
                                        </a>
                                    <?php else: ?>
                                        <a href="?page=contact" 
                                           class="btn btn-light btn-lg px-4">
                                            <i class="fas fa-envelope me-2"></i>Contact Admin
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <a href="<?php echo BASE_URL; ?>/login" 
                                       class="btn btn-light btn-lg px-4">
                                        <i class="fas fa-sign-in-alt me-2"></i>Login to Contact
                                    </a>
                                <?php endif; ?>
                                
                                <a href="mailto:admin@harumbakery.com" 
                                   class="btn btn-outline-light btn-lg px-4">
                                    <i class="fas fa-at me-2"></i>Email Us
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-4 text-center">
                            <div class="admin-avatar">
                                <i class="fas fa-headset fa-6x"></i>
                                <div class="mt-3">
                                    <h5 class="fw-bold">Admin Support</h5>
                                    <p class="mb-0">Response time: < 1 hour</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="row">
        <div class="col-12">
            <h2 class="h1 fw-bold text-dark text-center mb-4">Frequently Asked Questions</h2>
            
            <div class="accordion" id="faqAccordion">
                <!-- FAQ 1 -->
                <div class="accordion-item border-0 shadow-sm mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            <i class="fas fa-question-circle text-primary me-2"></i>
                            Bagaimana cara memesan?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            1. Login/Register akun<br>
                            2. Pilih produk di halaman Products<br>
                            3. Tambah ke Cart<br>
                            4. Checkout dan bayar<br>
                            5. Tunggu konfirmasi dari admin
                        </div>
                    </div>
                </div>
                
                <!-- FAQ 2 -->
                <div class="accordion-item border-0 shadow-sm mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            <i class="fas fa-question-circle text-primary me-2"></i>
                            Berapa lama pengiriman?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Dalam kota: 1-2 jam<br>
                            Luar kota: 1-2 hari<br>
                            *Bergantung pada lokasi dan ketersediaan kurir
                        </div>
                    </div>
                </div>
                
                <!-- FAQ 3 -->
                <div class="accordion-item border-0 shadow-sm mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            <i class="fas fa-question-circle text-primary me-2"></i>
                            Bagaimana cara menghubungi admin?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            1. Login ke akun Anda<br>
                            2. Klik tombol "Contact Admin" di atas<br>
                            3. Isi form yang tersedia<br>
                            4. Atau email langsung ke: admin@harumbakery.com
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #B99976;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -25px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #B99976;
    border: 3px solid white;
    box-shadow: 0 0 0 3px #B99976;
}

.timeline-date {
    font-weight: bold;
    color: #B99976;
    margin-bottom: 5px;
}

.admin-avatar i {
    filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
}

.accordion-button {
    font-weight: 600;
    background-color: white;
}

.accordion-button:not(.collapsed) {
    background-color: #FFF0F5;
    color: #D63384;
    box-shadow: none;
}

.card {
    border-radius: 15px;
    overflow: hidden;
}
</style>

<?php include __DIR__ . '/../../views/footer.php'; ?>