// Controle de saisie des formulaires : message rouge si la saisie est
// incorrecte, vert si elle est correcte.
// (Le formulaire de cours a son propre fichier : cours-validation.js)

function afficherMessage(champ, correct, texte) {
  var groupe = champ.parentElement;
  var message = groupe.querySelector('.js-message');

  if (!message) {
    message = document.createElement('p');
    groupe.appendChild(message);
  }

  message.textContent = texte;
  message.className = 'js-message ' + (correct ? 'form-success' : 'form-error');

  if (correct) {
    champ.classList.remove('is-invalid');
  } else {
    champ.classList.add('is-invalid');
  }
}

// Chaque controle affiche son message et retourne true si le champ est correct.

function champRequis(champ, message) {
  var correct = champ.value.trim() !== '';
  afficherMessage(champ, correct, correct ? 'Correct' : message);
  return correct;
}

function champLongueurMin(champ, longueur, message) {
  var correct = champ.value.trim().length >= longueur;
  afficherMessage(champ, correct, correct ? 'Correct' : message);
  return correct;
}

// Lettres et espaces uniquement, 3 caracteres minimum (nom, prenom).
function champLettres(champ, message) {
  var valeur = champ.value.trim();
  var correct = valeur.length >= 3 && /^[a-zA-ZÀ-ÿ \-]+$/.test(valeur);
  afficherMessage(champ, correct, correct ? 'Correct' : message);
  return correct;
}

function champEmail(champ) {
  var valeur = champ.value.trim();
  var correct = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valeur);
  afficherMessage(champ, correct, correct ? 'Correct' : 'Merci de saisir une adresse email valide.');
  return correct;
}

function champMotDePasse(champ) {
  var correct = champ.value.length >= 8;
  afficherMessage(champ, correct, correct ? 'Correct' : 'Le mot de passe doit contenir au moins 8 caracteres.');
  return correct;
}

// Vide = correct : on ne change pas le mot de passe.
function champMotDePasseFacultatif(champ) {
  if (champ.value === '') {
    afficherMessage(champ, true, 'Laissez vide pour conserver le mot de passe actuel.');
    return true;
  }
  return champMotDePasse(champ);
}

function champConfirmation(champ, reference) {
  var correct = champ.value !== '' && champ.value === reference.value;
  afficherMessage(champ, correct, correct ? 'Correct' : 'Les deux mots de passe ne correspondent pas.');
  return correct;
}

document.addEventListener('DOMContentLoaded', function () {

  // --- Formulaire de connexion ---
  var formConnexion = document.getElementById('form-connexion');
  if (formConnexion) {
    var emailConnexion = document.getElementById('email');
    var mdpConnexion = document.getElementById('mot_de_passe');

    formConnexion.addEventListener('submit', function (event) {
      var emailOk = champEmail(emailConnexion);
      var mdpOk = champRequis(mdpConnexion, 'Le mot de passe est obligatoire.');

      if (!emailOk || !mdpOk) {
        event.preventDefault();
      }
    });

    emailConnexion.addEventListener('blur', function () { champEmail(emailConnexion); });
  }

  // --- Formulaire d'inscription (nouveau compte etudiant) ---
  var formInscription = document.getElementById('form-inscription');
  if (formInscription) {
    var prenomI = document.getElementById('prenom');
    var nomI = document.getElementById('nom');
    var emailI = document.getElementById('email');
    var mdpI = document.getElementById('mot_de_passe');
    var confirmationI = document.getElementById('mot_de_passe_confirmation');

    formInscription.addEventListener('submit', function (event) {
      var prenomOk = champLettres(prenomI, 'Le prenom doit contenir au moins 3 lettres, sans chiffre.');
      var nomOk = champLettres(nomI, 'Le nom doit contenir au moins 3 lettres, sans chiffre.');
      var emailOk = champEmail(emailI);
      var mdpOk = champMotDePasse(mdpI);
      var confirmationOk = champConfirmation(confirmationI, mdpI);

      if (!prenomOk || !nomOk || !emailOk || !mdpOk || !confirmationOk) {
        event.preventDefault();
      }
    });

    prenomI.addEventListener('blur', function () { champLettres(prenomI, 'Le prenom doit contenir au moins 3 lettres, sans chiffre.'); });
    nomI.addEventListener('blur', function () { champLettres(nomI, 'Le nom doit contenir au moins 3 lettres, sans chiffre.'); });
    emailI.addEventListener('blur', function () { champEmail(emailI); });
    mdpI.addEventListener('keyup', function () { champMotDePasse(mdpI); });
    confirmationI.addEventListener('keyup', function () { champConfirmation(confirmationI, mdpI); });
  }

  // --- Modification du profil (nom / prenom / bio) ---
  var formProfil = document.getElementById('form-profil');
  if (formProfil) {
    var prenomP = document.getElementById('prenom');
    var nomP = document.getElementById('nom');

    formProfil.addEventListener('submit', function (event) {
      var prenomOk = champLettres(prenomP, 'Le prenom doit contenir au moins 3 lettres, sans chiffre.');
      var nomOk = champLettres(nomP, 'Le nom doit contenir au moins 3 lettres, sans chiffre.');

      if (!prenomOk || !nomOk) {
        event.preventDefault();
      }
    });

    prenomP.addEventListener('blur', function () { champLettres(prenomP, 'Le prenom doit contenir au moins 3 lettres, sans chiffre.'); });
    nomP.addEventListener('blur', function () { champLettres(nomP, 'Le nom doit contenir au moins 3 lettres, sans chiffre.'); });
  }

  // --- Changement de mot de passe ---
  var formMotDePasse = document.getElementById('form-mot-de-passe');
  if (formMotDePasse) {
    var actuel = document.getElementById('mot_de_passe_actuel');
    var nouveau = document.getElementById('mot_de_passe');
    var confirmationM = document.getElementById('mot_de_passe_confirmation');

    formMotDePasse.addEventListener('submit', function (event) {
      var actuelOk = champRequis(actuel, 'Le mot de passe actuel est obligatoire.');
      var nouveauOk = champMotDePasse(nouveau);
      var confirmationOk = champConfirmation(confirmationM, nouveau);

      if (!actuelOk || !nouveauOk || !confirmationOk) {
        event.preventDefault();
      }
    });

    nouveau.addEventListener('keyup', function () { champMotDePasse(nouveau); });
    confirmationM.addEventListener('keyup', function () { champConfirmation(confirmationM, nouveau); });
  }

  // --- Ajout d'un utilisateur par l'administrateur (mot de passe obligatoire) ---
  var formUtilisateurAjouter = document.getElementById('form-utilisateur-ajouter');
  if (formUtilisateurAjouter) {
    var prenomA = document.getElementById('prenom');
    var nomA = document.getElementById('nom');
    var emailA = document.getElementById('email');
    var mdpA = document.getElementById('mot_de_passe');

    formUtilisateurAjouter.addEventListener('submit', function (event) {
      var prenomOk = champLettres(prenomA, 'Le prenom doit contenir au moins 3 lettres, sans chiffre.');
      var nomOk = champLettres(nomA, 'Le nom doit contenir au moins 3 lettres, sans chiffre.');
      var emailOk = champEmail(emailA);
      var mdpOk = champMotDePasse(mdpA);

      if (!prenomOk || !nomOk || !emailOk || !mdpOk) {
        event.preventDefault();
      }
    });

    prenomA.addEventListener('blur', function () { champLettres(prenomA, 'Le prenom doit contenir au moins 3 lettres, sans chiffre.'); });
    nomA.addEventListener('blur', function () { champLettres(nomA, 'Le nom doit contenir au moins 3 lettres, sans chiffre.'); });
    emailA.addEventListener('blur', function () { champEmail(emailA); });
  }

  // --- Modification d'un utilisateur par l'administrateur (mot de passe facultatif) ---
  var formUtilisateurModifier = document.getElementById('form-utilisateur-modifier');
  if (formUtilisateurModifier) {
    var prenomU = document.getElementById('prenom');
    var nomU = document.getElementById('nom');
    var emailU = document.getElementById('email');
    var mdpU = document.getElementById('mot_de_passe');

    formUtilisateurModifier.addEventListener('submit', function (event) {
      var prenomOk = champLettres(prenomU, 'Le prenom doit contenir au moins 3 lettres, sans chiffre.');
      var nomOk = champLettres(nomU, 'Le nom doit contenir au moins 3 lettres, sans chiffre.');
      var emailOk = champEmail(emailU);
      var mdpOk = champMotDePasseFacultatif(mdpU);

      if (!prenomOk || !nomOk || !emailOk || !mdpOk) {
        event.preventDefault();
      }
    });

    prenomU.addEventListener('blur', function () { champLettres(prenomU, 'Le prenom doit contenir au moins 3 lettres, sans chiffre.'); });
    nomU.addEventListener('blur', function () { champLettres(nomU, 'Le nom doit contenir au moins 3 lettres, sans chiffre.'); });
    emailU.addEventListener('blur', function () { champEmail(emailU); });
  }

  // --- Categorie de cours (ajout et modification) ---
  var formCategorie = document.getElementById('form-categorie');
  if (formCategorie) {
    var nomCategorie = document.getElementById('nom');

    formCategorie.addEventListener('submit', function (event) {
      if (!champLongueurMin(nomCategorie, 3, 'Le nom doit contenir au moins 3 caracteres.')) {
        event.preventDefault();
      }
    });

    nomCategorie.addEventListener('keyup', function () {
      champLongueurMin(nomCategorie, 3, 'Le nom doit contenir au moins 3 caracteres.');
    });
  }

  // --- Module d'un cours (ajout et modification) ---
  var formModule = document.getElementById('form-module');
  if (formModule) {
    var titreModule = document.getElementById('titre');
    var ordreModule = document.getElementById('ordre');

    formModule.addEventListener('submit', function (event) {
      var titreOk = champLongueurMin(titreModule, 3, 'Le titre doit contenir au moins 3 caracteres.');
      var ordreOk = true;

      if (ordreModule.value.trim() !== '') {
        ordreOk = Number(ordreModule.value) >= 1;
        afficherMessage(ordreModule, ordreOk, ordreOk ? 'Correct' : "L'ordre doit etre un entier positif.");
      }

      if (!titreOk || !ordreOk) {
        event.preventDefault();
      }
    });

    titreModule.addEventListener('keyup', function () {
      champLongueurMin(titreModule, 3, 'Le titre doit contenir au moins 3 caracteres.');
    });
  }

  // --- Ressource d'un module (ajout et modification) ---
  var formRessource = document.getElementById('form-ressource');
  if (formRessource) {
    var titreRessource = document.getElementById('titre');
    var typeRessource = document.getElementById('type');
    var contenuRessource = document.getElementById('contenu');
    var fichierRessource = document.getElementById('fichier');

    formRessource.addEventListener('submit', function (event) {
      var titreOk = champLongueurMin(titreRessource, 3, 'Le titre doit contenir au moins 3 caracteres.');

      // Soit un fichier, soit une URL.
      var contenuOk = contenuRessource.value.trim() !== ''
        || (fichierRessource && fichierRessource.value !== '')
        || formRessource.getAttribute('data-contenu-existant') === '1';
      afficherMessage(contenuRessource, contenuOk, contenuOk ? 'Correct' : 'Fournissez un fichier ou une URL.');

      if (!titreOk || !contenuOk) {
        event.preventDefault();
      }
    });

    titreRessource.addEventListener('keyup', function () {
      champLongueurMin(titreRessource, 3, 'Le titre doit contenir au moins 3 caracteres.');
    });

    typeRessource.addEventListener('change', function () {
      if (typeRessource.value === 'document') {
        afficherMessage(typeRessource, true, 'Televersez un fichier (PDF, Word, PowerPoint, ZIP ou CSV).');
      } else {
        afficherMessage(typeRessource, true, 'Indiquez l\'URL de la video ou du quiz.');
      }
    });
  }
});
