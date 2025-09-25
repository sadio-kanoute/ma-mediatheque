<div class="page-header">
    <div class="container">
        <h1><?php e($title); ?></h1>
    </div>
</div>

<section class="content">
    <div class="container">
        <div class="content-grid">
            <div class="content-main">
                <h2>Nous contacter</h2>
                <p>N'hésitez pas à nous envoyer un message. Nous vous répondrons dans les plus brefs délais.</p>
                
                <form method="POST" class="contact-form">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    
                    <div class="form-group">
                        <label for="name">Nom complet</label>
                        <input type="text" id="name" name="name" required 
                               value="<?php echo escape(post('name', '')); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Adresse email</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo escape(post('email', '')); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="6" required><?php echo escape(post('message', '')); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        Envoyer le message
                    </button>
                </form>
            </div>
            
            <div class="sidebar">
                <div class="info-box">
                    <h4>Informations de contact</h4>
                    <p><i class="fas fa-envelope"></i> contact@example.com</p>
                    <p><i class="fas fa-phone"></i> +33 1 23 45 67 89</p>
                    <p><i class="fas fa-map-marker-alt"></i> 123 Rue Example, 75000 Paris</p>
                </div>
                
                <div class="info-box">
                    <h4>Horaires d'ouverture</h4>
                    <p><strong>Lundi - Vendredi :</strong> 9h00 - 18h00</p>
                    <p><strong>Samedi :</strong> 9h00 - 12h00</p>
                    <p><strong>Dimanche :</strong> Fermé</p>
                </div>
            </div>
        </div>
    </div>
</section> 