<?php
class ModuleController
{
    private $model;

    public function __construct()
    {
        $this->model = new Module();
    }

    public function creer($coursId, $data)
    {
        $errors = $this->valider($data);

        if ($errors === []) {
            $ordre = $data['ordre'] !== '' ? (int) $data['ordre'] : $this->model->prochainOrdre($coursId);
            $this->model->create([
                'cours_id' => $coursId,
                'titre' => $data['titre'],
                'description' => $data['description'],
                'ordre' => $ordre,
            ]);
        }

        return $errors;
    }

    public function modifier($id, $data)
    {
        $errors = $this->valider($data);

        if ($errors === []) {
            $module = $this->model->find($id);
            $ordre = $data['ordre'] !== '' ? (int) $data['ordre'] : (int) $module['ordre'];
            $this->model->update($id, [
                'titre' => $data['titre'],
                'description' => $data['description'],
                'ordre' => $ordre,
            ]);
        }

        return $errors;
    }

    public function supprimer($id)
    {
        $this->model->delete($id);
    }

    // Null si le module n'existe pas ou n'appartient pas a ce formateur.
    public function trouverPourFormateur($id, $formateurId)
    {
        $module = $this->model->findAvecCours($id);

        if (!$module || (int) $module['formateur_id'] !== (int) $formateurId) {
            return null;
        }

        return $module;
    }

    private function valider($data)
    {
        $errors = [];
        $titre = trim($data['titre']);
        $ordre = trim($data['ordre']);

        if ($titre === '') {
            $errors['titre'] = 'Le titre du module est obligatoire.';
        } elseif (mb_strlen($titre) < 3) {
            $errors['titre'] = 'Le titre doit contenir au moins 3 caracteres.';
        } elseif (mb_strlen($titre) > 150) {
            $errors['titre'] = 'Le titre ne doit pas depasser 150 caracteres.';
        }

        if ($ordre !== '' && (!is_numeric($ordre) || (int) $ordre < 1)) {
            $errors['ordre'] = "L'ordre doit etre un entier positif.";
        }

        return $errors;
    }
}
