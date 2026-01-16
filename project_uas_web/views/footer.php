<?php

?>
        <!-- Footer -->
        <footer class="footer mt-auto py-4 bg-light border-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 mb-4">
                        <h5 class="mb-3">
                            <i class="fas fa-cupcake text-primary me-2"></i>
                            <span class="fw-bold" style="color: #B99976;">Harum Bakery</span>
                        </h5>
                        <p class="text-muted">
                            Menyediakan aneka roti dan kue terbaik dengan cita rasa tradisional 
                            dan modern yang selalu fresh setiap hari.
                        </p>
                        <div class="social-icons">
                            <a href="#" class="text-decoration-none me-3">
                                <i class="fab fa-facebook fa-lg" style="color: #B99976;"></i>
                            </a>
                            <a href="#" class="text-decoration-none me-3">
                                <i class="fab fa-instagram fa-lg" style="color: #B99976;"></i>
                            </a>
                            <a href="#" class="text-decoration-none me-3">
                                <i class="fab fa-whatsapp fa-lg" style="color: #B99976;"></i>
                            </a>
                            <a href="#" class="text-decoration-none">
                                <i class="fab fa-tiktok fa-lg" style="color: #B99976;"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-6 mb-4">
                        <h6 class="fw-bold mb-3" style="color: #987554;">Quick Links</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="<?php echo BASE_URL; ?>/dashboard" class="text-decoration-none text-muted hover-pink">
                                    <i class="fas fa-home me-2"></i>Home
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="<?php echo BASE_URL; ?>/modules/product" class="text-decoration-none text-muted hover-pink">
                                    <i class="fas fa-bread-slice me-2"></i>Products
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="<?php echo BASE_URL; ?>/category/cakes" class="text-decoration-none text-muted hover-pink">
                                    <i class="fas fa-birthday-cake me-2"></i>Cakes
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="<?php echo BASE_URL; ?>/category/bread" class="text-decoration-none text-muted hover-pink">
                                    <i class="fas fa-cookie-bite me-2"></i>Bread
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-4">
                        <h6 class="fw-bold mb-3" style="color: #987554;">Customer Service</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="#" class="text-decoration-none text-muted hover-pink">
                                    <i class="fas fa-question-circle me-2"></i>Help Center
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="#" class="text-decoration-none text-muted hover-pink">
                                    <i class="fas fa-truck me-2"></i>Shipping Info
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="#" class="text-decoration-none text-muted hover-pink">
                                    <i class="fas fa-exchange-alt me-2"></i>Returns
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="#" class="text-decoration-none text-muted hover-pink">
                                    <i class="fas fa-phone me-2"></i>Contact Us
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="col-lg-3 mb-4">
                        <h6 class="fw-bold mb-3" style="color: #987554;">Contact Info</h6>
                        <ul class="list-unstyled text-muted">
                            <li class="mb-2">
                                <i class="fas fa-map-marker-alt me-2" style="color: #B99976;"></i>
                                Jl. Raya Bakery No. 123, Jakarta
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-phone me-2" style="color: #B99976;"></i>
                                +62 812 3456 7890
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-envelope me-2" style="color: #B99976;"></i>
                                info@harumbakery.com
                            </li>
                            <li>
                                <i class="fas fa-clock me-2" style="color: #B99976;"></i>
                                Open: 08:00 - 20:00
                            </li>
                        </ul>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <p class="mb-0 text-muted">
                            &copy; <?php echo date('Y'); ?> Harum Bakery. All rights reserved.
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="#" class="text-decoration-none text-muted me-3 hover-pink">Privacy Policy</a>
                        <a href="#" class="text-decoration-none text-muted me-3 hover-pink">Terms of Service</a>
                        <a href="#" class="text-decoration-none text-muted hover-pink">Sitemap</a>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Back to Top Button -->
        <button id="backToTop" class="btn btn-primary btn-floating" title="Back to top">
            <i class="fas fa-chevron-up"></i>
        </button>

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
        
        <!-- Custom Scripts -->
        <script>
        // Back to Top Button
        const backToTopButton = document.getElementById('backToTop');
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.style.display = 'block';
            } else {
                backToTopButton.style.display = 'none';
            }
        });
        
        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Password toggle functionality (if not already in login.php)
        const togglePassword = document.getElementById('togglePassword');
        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const password = document.getElementById('password');
                const icon = this.querySelector('i');
                
                if (password.type === 'password') {
                    password.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    password.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        }
        
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
        
        // Form validation feedback
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const requiredInputs = this.querySelectorAll('[required]');
                let isValid = true;
                
                requiredInputs.forEach(input => {
                    if (!input.value.trim()) {
                        isValid = false;
                        input.classList.add('is-invalid');
                        
                        // Add feedback message
                        if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('invalid-feedback')) {
                            const feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            feedback.textContent = 'This field is required';
                            input.parentNode.appendChild(feedback);
                        }
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
        </script>
        
        <!-- Custom CSS for Footer -->
        <style>
        .footer {
            background: linear-gradient(to bottom, #F3E9DC 0%, #F3E9D7 100%);
        }
        
        .hover-pink {
            transition: all 0.3s ease;
        }
        
        .hover-pink:hover {
            color: #987554 !important;
            padding-left: 5px;
        }
        
        .btn-floating {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: none;
            z-index: 1000;
            background-color: #B99976;
            border: none;
            box-shadow: 0 4px 12px rgba(255, 182, 193, 0.3);
            transition: all 0.3s ease;
        }
        
        .btn-floating:hover {
            background-color: #987554;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(255, 182, 193, 0.4);
        }
        
        .social-icons a {
            transition: transform 0.3s ease;
        }
        
        .social-icons a:hover {
            transform: translateY(-3px);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .footer .col-lg-4,
            .footer .col-lg-2,
            .footer .col-lg-3 {
                margin-bottom: 2rem;
            }
            
            .btn-floating {
                width: 45px;
                height: 45px;
                bottom: 15px;
                right: 15px;
            }
        }
        </style>
    </body>
</html>

