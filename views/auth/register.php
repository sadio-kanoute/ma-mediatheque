<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1><?php e($title); ?></h1>
            <p>Créez votre compte</p>
        </div>
        
        <form method="POST" class="auth-form" action="<?php echo url('auth/register'); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            
            <div class="form-group">
                <label for="name">Prenom </label>
                <input type="text" id="name" name="name" required 
                       value="<?php echo escape(post('name', '')); ?>"
                       placeholder="Votre prenom "
                       pattern="[A-Za-zÀ-ÿ\s\-]{2,50}"
                       title="2 à 50 lettres, espaces et tirets uniquement">

            </div>

            <div class="form-group">
                <label for="last_name">Nom</label>
                <input type="text" id="last_name" name="last_name" required 
                    value="<?php echo escape(post('last_name', '')); ?>"
                    placeholder="Votre nom"
                    pattern="[A-Za-zÀ-ÿ\s\-]{2,50}"
                    title="2 à 50 lettres, espaces et tirets uniquement">
            </div>

            
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo escape(post('email', '')); ?>"
                       placeholder="votre@email.com">
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required
                    placeholder="Au moins 8 caractères"
                    pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$"
                    title="Minimum 8 caractères avec au moins 1 majuscule, 1 minuscule et 1 chiffre">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                    placeholder="Confirmez votre mot de passe">
            </div>

            
            <button type="submit" class="btn btn-primary btn-full">
                <i class="fas fa-user-plus"></i>
                S'inscrire
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Déjà un compte ? 
                <a href="<?php echo url('auth/login'); ?>">Se connecter</a>
            </p>
        </div>
    </div>
</div> 