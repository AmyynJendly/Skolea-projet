<?php
class CoursController
{
    private $model;

    public function __construct()
    {
        $this->model = new Cours();
    }

    // Retourne [tableau d'erreurs, id du cours cree ou null].
    public function creer($data, $formateurId)
    {
        $errors = $this->valider($data);

        if ($errors !== []) {
            return [$errors, null];
        }

        $id = $this->model->create([
            'titre' => $data['titre'],
            'description' => $data['description'],
            'categorie_id' => $data['categorie_id'],
            'formateur_id' => $formateurId,
            'niveau' => $data['niveau'],
            'statut' => $data['statut'],
        ]);

        return [$errors, $id];
    }

    public function modifier($id, $data)
    {
        $errors = $this->valider($data);

        if ($errors === []) {
            $this->model->update($id, [
                'titre' => $data['titre'],
                'description' => $data['description'],
                'categorie_id' => $data['categorie_id'],
                'niveau' => $data['niveau'],
                'statut' => $data['statut'],
            ]);
        }

        return $errors;
    }

    public function supprimer($id)
    {
        $this->model->delete($id);
    }

    public function afficherCours($cours)
    {
        $cours->show();
    }

    private function valider($data)
    {
        $errors = [];
        $titre = trim($data['titre']);
        $description = trim($data['description']);
        $categorieId = trim($data['categorie_id']);

        if ($titre === '') {
            $errors['titre'] = 'Le titre du cours est obligatoire.';
        } elseif (mb_strlen($titre) < 3) {
            $errors['titre'] = 'Le titre doit contenir au moins 3 caracteres.';
        } elseif (mb_strlen($titre) > 150) {
            $errors['titre'] = 'Le titre ne doit pas depasser 150 caracteres.';
        }

        if ($description === '') {
            $errors['description'] = 'La description est obligatoire.';
        }

        if ($categorieId === '') {
            $errors['categorie_id'] = 'Merci de choisir une categorie.';
        } elseif (!is_numeric($categorieId)) {
            $errors['categorie_id'] = 'Categorie invalide.';
        } else {
            $categorieModel = new Categorie();
            if (!$categorieModel->find((int) $categorieId)) {
                $errors['categorie_id'] = 'Categorie invalide.';
            }
        }

        if (!in_array($data['niveau'], ['debutant', 'intermediaire', 'avance'], true)) {
            $errors['niveau'] = 'Le niveau doit etre Debutant, Intermediaire ou Avance.';
        }

        if (!in_array($data['statut'], ['brouillon', 'publie'], true)) {
            $errors['statut'] = 'Le statut doit etre Brouillon ou Publie.';
        }

        return $errors;
    }
}
