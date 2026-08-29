<?php
class CategorieController
{
    private $model;

    public function __construct()
    {
        $this->model = new Categorie();
    }

    // Retourne un tableau d'erreurs, vide si tout s'est bien passe.
    public function creer($data)
    {
        $errors = $this->valider($data, null);

        if ($errors === []) {
            $this->model->create($data);
        }

        return $errors;
    }

    public function modifier($id, $data)
    {
        $errors = $this->valider($data, $id);

        if ($errors === []) {
            $this->model->update($id, $data);
        }

        return $errors;
    }

    // Retourne un message d'erreur, ou null si la suppression a reussi.
    public function supprimer($id)
    {
        if ($this->model->countCours($id) > 0) {
            return 'Impossible de supprimer : des cours sont encore rattaches a cette categorie.';
        }

        try {
            $this->model->delete($id);
            return null;
        } catch (PDOException $e) {
            return 'Impossible de supprimer cette categorie.';
        }
    }

    private function valider($data, $idActuel)
    {
        $errors = [];
        $nom = trim($data['nom']);
        $description = trim($data['description']);

        if ($nom === '') {
            $errors['nom'] = 'Le nom de la categorie est obligatoire.';
        } elseif (mb_strlen($nom) < 3) {
            $errors['nom'] = 'Le nom doit contenir au moins 3 caracteres.';
        } elseif (mb_strlen($nom) > 100) {
            $errors['nom'] = 'Le nom ne doit pas depasser 100 caracteres.';
        } elseif ($this->model->nomExists($nom, $idActuel)) {
            $errors['nom'] = 'Une categorie porte deja ce nom.';
        }

        if (mb_strlen($description) > 255) {
            $errors['description'] = 'La description ne doit pas depasser 255 caracteres.';
        }

        return $errors;
    }
}
