<?php
class RessourceController
{
    private $model;

    public function __construct()
    {
        $this->model = new Ressource();
    }

    public function creer($moduleId, $data, $fichier)
    {
        list($errors, $contenu) = $this->valider($data, $fichier, true);

        if ($errors === []) {
            $this->model->create([
                'module_id' => $moduleId,
                'titre' => $data['titre'],
                'type' => $data['type'],
                'contenu' => $contenu,
                'description' => $data['description'],
            ]);
        }

        return $errors;
    }

    public function modifier($id, $data, $fichier)
    {
        list($errors, $contenu) = $this->valider($data, $fichier, false);

        if ($errors === []) {
            $this->model->update($id, [
                'titre' => $data['titre'],
                'type' => $data['type'],
                'contenu' => $contenu,
                'description' => $data['description'],
            ]);
        }

        return $errors;
    }

    public function supprimer($id)
    {
        $this->model->delete($id);
    }

    // Null si la ressource n'existe pas ou n'appartient pas a ce formateur.
    public function trouverPourFormateur($id, $formateurId)
    {
        $ressource = $this->model->findAvecCours($id);

        if (!$ressource || (int) $ressource['formateur_id'] !== (int) $formateurId) {
            return null;
        }

        return $ressource;
    }

    // Retourne [tableau d'erreurs, contenu a enregistrer].
    private function valider($data, $fichier, $obligatoire)
    {
        $errors = [];
        $titre = trim($data['titre']);

        if ($titre === '') {
            $errors['titre'] = 'Le titre de la ressource est obligatoire.';
        } elseif (mb_strlen($titre) < 3) {
            $errors['titre'] = 'Le titre doit contenir au moins 3 caracteres.';
        } elseif (mb_strlen($titre) > 150) {
            $errors['titre'] = 'Le titre ne doit pas depasser 150 caracteres.';
        }

        if (!in_array($data['type'], ['document', 'video', 'quiz'], true)) {
            $errors['type'] = 'Le type doit etre Document, Video ou Quiz.';
        }

        $contenu = trim($data['contenu']) !== '' ? trim($data['contenu']) : null;

        if ($data['type'] === 'document') {
            try {
                $fichierStocke = Upload::stocker($fichier, 'ressources', ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'csv'], 8 * 1024 * 1024);
                if ($fichierStocke !== null) {
                    $contenu = $fichierStocke;
                }
            } catch (RuntimeException $e) {
                $errors['fichier'] = $e->getMessage();
            }
        }

        if ($obligatoire && $contenu === null && !isset($errors['fichier'])) {
            $errors['contenu'] = 'Fournissez un fichier ou une URL pour cette ressource.';
        }

        return [$errors, $contenu];
    }
}
