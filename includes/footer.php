</div>
    </div>
    <footer class="footer mt-5 py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                <div class="footer-copyright">
                        &copy; <?php echo date('Y'); ?> Inventory Management System. All rights reserved.
                    </div>
                </div>
                <div class="col-md-6 text-md-right">
                    
                    <div class="footer-links">
                        <a href="#" class="footer-link"><i class="fas fa-question-circle"></i> Help</a>
                        <a href="#" class="footer-link"><i class="fas fa-shield-alt"></i> Privacy</a>
                        <a href="#" class="footer-link"><i class="fas fa-file-contract"></i> Terms</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
    $(document).ready(function() {
        // Fade out alerts after 5 seconds
        $(".alert").delay(5000).fadeOut(500);
        
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Initialize popovers
        $('[data-toggle="popover"]').popover();
        
        // Add animation class to cards
        $(".card").addClass("card-animate");
        
        // Fix for modals on mobile
        $('.modal').on('show.bs.modal', function () {
            $('.modal-content').css('height','auto');
            $('.modal-dialog').css('margin-top', Math.max(0, ($(window).height() - $('.modal-dialog').height()) / 2));
        });
        
        // Ensure modals work properly on all devices
        $('.modal').on('shown.bs.modal', function() {
            $(this).find('[autofocus]').focus();
        });
    });
    </script>
    
    <style>
    /* Additional Global Styles */
    :root {
        --primary-color: #4e73df;
        --secondary-color: #1cc88a;
        --accent-color: #f6c23e;
        --danger-color: #e74a3b;
        --dark-color: #5a5c69;
        --light-color: #f8f9fc;
    }
    
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fc;
        color: #5a5c69;
    }
    
    /* Card Styles */
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        margin-bottom: 24px;
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .card-header {
        background-color: white;
        border-bottom: 1px solid #e3e6f0;
        padding: 1rem 1.25rem;
        border-top-left-radius: 10px !important;
        border-top-right-radius: 10px !important;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .card-header i {
        margin-right: 10px;
        color: var(--primary-color);
    }
    
    .card-body {
        padding: 1.25rem;
    }
    
    /* Button Styles */
    .btn {
        border-radius: 50px;
        padding: 0.375rem 1.2rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .btn-success {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
    }
    
    .btn-warning {
        background-color: var(--accent-color);
        border-color: var(--accent-color);
    }
    
    .btn-danger {
        background-color: var(--danger-color);
        border-color: var(--danger-color);
    }
    
    /* Table Styles */
    .table {
        color: #5a5c69;
    }
    
    .table thead th {
        background-color: #f8f9fc;
        border-top: none;
        border-bottom: 2px solid #e3e6f0;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(78, 115, 223, 0.05);
    }
    
    /* Form Styles */
    .form-control {
        border-radius: 10px;
        padding: 0.5rem 1rem;
        border: 1px solid #d1d3e2;
        font-size: 0.9rem;
    }
    
    .form-control:focus {
        border-color: #bac8f3;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    
    label {
        font-weight: 600;
        color: #5a5c69;
        margin-bottom: 0.5rem;
    }
    
    /* Alert Styles */
    .alert {
        border-radius: 10px;
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
    }
    
    /* Footer Styles */
    .footer {
        background-color: white;
        box-shadow: 0 -0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        color: #5a5c69;
    }
    
    .footer-logo {
        font-weight: 700;
        font-size: 1.5rem;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }
    
    .footer-text {
        color: #858796;
        margin-bottom: 0;
    }
    
    .footer-copyright {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .footer-links {
        display: flex;
        justify-content: flex-end;
    }
    
    .footer-link {
        color: var(--primary-color);
        margin-left: 1.5rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .footer-link:hover {
        color: #2e59d9;
        text-decoration: none;
    }
    
    /* Stat Card Styles */
    .stat-card {
        border-left: 4px solid;
        border-radius: 10px;
    }
    
    .stat-card-primary {
        border-left-color: var(--primary-color);
    }
    
    .stat-card-success {
        border-left-color: var(--secondary-color);
    }
    
    .stat-card-warning {
        border-left-color: var(--accent-color);
    }
    
    .stat-card-danger {
        border-left-color: var(--danger-color);
    }
    
    .stat-card-icon {
        font-size: 2rem;
        color: #dddfeb;
    }
    
    .stat-card-title {
        text-transform: uppercase;
        font-size: 0.7rem;
        font-weight: 700;
        color: #b7b9cc;
        letter-spacing: 0.1rem;
    }
    
    .stat-card-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #5a5c69;
    }
    
    /* Modal Styles */
    .modal-content {
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }
    
    .modal-header {
        background-color: var(--primary-color);
        color: white;
        border-bottom: none;
    }
    
    .modal-title {
        font-weight: 600;
    }
    
    .modal-footer {
        border-top: 1px solid #e3e6f0;
    }
    
    .close {
        color: white;
        text-shadow: none;
        opacity: 0.8;
    }
    
    .close:hover {
        color: white;
        opacity: 1;
    }
    
    /* Table Responsive Styles */
    .table-responsive {
        border-radius: 10px;
        overflow-x: auto;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .footer-links {
            justify-content: flex-start;
            margin-top: 1rem;
        }
        
        .footer-link {
            margin-left: 0;
            margin-right: 1.5rem;
        }
        
        .table-responsive {
            -webkit-overflow-scrolling: touch;
        }
        
        .table {
            min-width: 650px;
        }
        
        .stat-card-value {
            font-size: 1.2rem;
        }
        
        .card-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .card-header .btn {
            margin-top: 0.5rem;
            align-self: flex-start;
        }
        
        .d-flex.justify-content-between {
            flex-direction: column;
        }
        
        .d-flex.justify-content-between .btn {
            margin-top: 1rem;
            align-self: flex-start;
        }
    }
    
    @media (max-width: 576px) {
        .footer-logo {
            font-size: 1.2rem;
        }
        
        .footer-links {
            flex-wrap: wrap;
        }
        
        .footer-link {
            margin-bottom: 0.5rem;
        }
        
        .modal-dialog {
            margin: 0.5rem;
        }
        
        .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }
        
        .btn-group {
            width: 100%;
        }
        
        .btn-group .btn {
            width: auto;
        }
    }
    </style>
</body>
</html>

