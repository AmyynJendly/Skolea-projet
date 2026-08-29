<?php
class UtilisateurController
{
    private $model;

    public function __construct()
    {
        $this->model = new Utilisateur();
    }

    // Retourne [utilisateur ou null, tableau d'erreurs].
    public function connecter($email, $motDePasse)
    {
        $errors = [];
        $email = trim($email);

        if ($email === '') {
            $errors['email'] = "L'adresse email est obligatoire.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Merci de saisir une adresse email valide.';
        }

        if ($motDePasse === '') {
            $errors['mot_de_passe'] = 'Le mot de passe est obligatoire.';
        }

        if ($errors !== []) {
            return [null, $errors];
        }

        $utilisateur = $this->model->findByEmail($email);

        if (!$utilisateur || !password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
            $errors['email'] = 'Email ou mot de passe incorrect.';
            return [null, $errors];
        }

        return [$utilisateur, $errors];
    }

    // Cree toujours un compte etudiant.
    public function inscrire($data)
    {
        $errors = [];

        $erreurNom = $this->validerNomOuPrenom(trim($data['nom']), 'Nom');
        if ($erreurNom !== null) {
            $errors['nom'] = $erreurNom;
        }

        $erreurPrenom = $this->validerNomOuPrenom(trim($data['prenom']), 'Prenom');
        if ($erreurPrenom !== null) {
            $errors['prenom'] = $erreurPrenom;
        }

        $erreurEmail = $this->validerEmail(trim($data['email']), null);
        if ($erreurEmail !== null) {
            $errors['email'] = $erreurEmail;
        }

        $erreurMotDePasse = $this->validerMotDePasse($data['mot_de_passe'], $data['mot_de_passe_confirmation']);
        if ($erreurMotDePasse !== null) {
            $errors[$erreurMotDePasse['champ']] = $erreurMotDePasse['message'];
        }

        if ($errors !== []) {
            return [null, $errors];
        }

        $id = $this->model->create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_DEFAULT),
            'role' => 'etudiant',
        ]);

        return [$this->model->findById($id), $errors];
    }

    // --- Gestion des comptes par l'administrateur ---

    public function creerParAdmin($data)
    {
        $errors = $this->validerFormulaireAdmin($data, true, null);

        if ($errors !== []) {
            return $errors;
        }

        $this->model->create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_DEFAULT),
            'role' => $data['role'],
            'bio' => $data['bio'],
        ]);

        return $errors;
    }

    public function modifierParAdmin($id, $data)
    {
        $errors = $this->validerFormulaireAdmin($data, false, $id);

        if ($errors !== []) {
            return $errors;
        }

        $this->model->update($id, $data);

        if ($data['mot_de_passe'] !== '') {
            $this->model->updatePassword($id, password_hash($data['mot_de_passe'], PASSWORD_DEFAULT));
        }

        return $errors;
    }

    public function supprimer($id)
    {
        $this->model->delete($id);
    }

    private function validerFormulaireAdmin($data, $creation, $idActuel)
    {
        $errors = [];

        $erreurNom = $this->validerNomOuPrenom(trim($data['nom']), 'Nom');
        if ($erreurNom !== null) {
            $errors['nom'] = $erreurNom;
        }

        $erreurPrenom = $this->validerNomOuPrenom(trim($data['prenom']), 'Prenom');
        if ($erreurPrenom !== null) {
            $errors['prenom'] = $erreurPrenom;
        }

        $erreurEmail = $this->validerEmail(trim($data['email']), $idActuel);
        if ($erreurEmail !== null) {
            $errors['email'] = $erreurEmail;
        }

        if (!in_array($data['role'], ['administrateur', 'formateur', 'etudiant'], true)) {
            $errors['role'] = 'Le role choisi est invalide.';
        }

        if ($creation && $data['mot_de_passe'] === '') {
            $errors['mot_de_passe'] = 'Le mot de passe est obligatoire.';
        } elseif ($data['mot_de_passe'] !== '' && mb_strlen($data['mot_de_passe']) < 8) {
            $errors['mot_de_passe'] = 'Le mot de passe doit contenir au moins 8 caracteres.';
        }

        return $errors;
    }

    // --- Profil (les 3 roles) ---

    public function modifierProfil($id, $data)
    {
        $errors = [];

        $erreurNom = $this->validerNomOuPrenom(trim($data['nom']), 'Nom');
        if ($erreurNom !== null) {
            $errors['nom'] = $erreurNom;
        }

        $erreurPrenom = $this->validerNomOuPrenom(trim($data['prenom']), 'Prenom');
        if ($erreurPrenom !== null) {
            $errors['prenom'] = $erreurPrenom;
        }

        if (mb_strlen(trim($data['bio'])) > 500) {
            $errors['bio'] = 'La bio ne doit pas depasser 500 caracteres.';
        }

        if ($errors === []) {
            $this->model->updateProfil($id, $data);
        }

        return $errors;
    }

    public function changerMotDePasse($id, $data)
    {
        $errors = [];
        $utilisateur = $this->model->findById($id);

        if ($data['mot_de_passe_actuel'] === '') {
            $errors['mot_de_passe_actuel'] = 'Le mot de passe actuel est obligatoire.';
        }

        $erreurMotDePasse = $this->validerMotDePasse($data['mot_de_passe'], $data['mot_de_passe_confirmation']);
        if ($erreurMotDePasse !== null) {
            $errors[$erreurMotDePasse['champ']] = $erreurMotDePasse['message'];
        }

        if ($errors === [] && !password_verify($data['mot_de_passe_actuel'], $utilisateur['mot_de_passe'])) {
            $errors['mot_de_passe_actuel'] = 'Mot de passe actuel incorrect.';
        }

        if ($errors === []) {
            $this->model->updatePassword($id, password_hash($data['mot_de_passe'], PASSWORD_DEFAULT));
        }

        return $errors;
    }

    // --- Controles reutilises : message d'erreur, ou null si correct ---

    private function validerNomOuPrenom($valeur, $label)
    {
        if ($valeur === '') {
            return "Le champ " . $label . " est obligatoire.";
        }
        if (mb_strlen($valeur) < 3) {
            return $label . " doit contenir au moins 3 caracteres.";
        }
        if (mb_strlen($valeur) > 80) {
            return $label . " ne doit pas depasser 80 caracteres.";
        }
        if (!preg_match('/^[a-zA-ZÀ-ÿ \-]+$/u', $valeur)) {
            return $label . " ne doit contenir que des lettres et des espaces.";
        }

        return null;
    }

    private function validerEmail($email, $idActuel)
    {
        if ($email === '') {
            return "L'adresse email est obligatoire.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Merci de saisir une adresse email valide.';
        }
        if (mb_strlen($email) > 150) {
            return "L'adresse email ne doit pas depasser 150 caracteres.";
        }
        if ($this->model->emailExists($email, $idActuel)) {
            return 'Cette adresse email est deja utilisee.';
        }

        return null;
    }

    // Retourne ['champ' => ..., 'message' => ...] ou null.
    private function validerMotDePasse($motDePasse, $confirmation)
    {
        if ($motDePasse === '') {
            return ['champ' => 'mot_de_passe', 'message' => 'Le mot de passe est obligatoire.'];
        }
        if (mb_strlen($motDePasse) < 8) {
            return ['champ' => 'mot_de_passe', 'message' => 'Le mot de passe doit contenir au moins 8 caracteres.'];
        }
        if ($motDePasse !== $confirmation) {
            return ['champ' => 'mot_de_passe_confirmation', 'message' => 'Les deux mots de passe ne correspondent pas.'];
        }

        return null;
    }
}
