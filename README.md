# 🏨 Système de Gestion de Réservation d'Hôtel

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

Une application web complète et moderne pour la gestion des réservations d'hôtel, développée en PHP et MySQL, avec une interface utilisateur élégante et responsive.

## ✨ Fonctionnalités

### 🔐 Authentification
- Inscription et connexion des utilisateurs
- Sécurisation des sessions et mots de passe
- Rôles utilisateurs (client, administrateur)

### 🛌 Gestion des Chambres
- Catalogue de chambres avec différentes catégories
- Affichage des détails (prix, capacité, équipements)
- Images de haute qualité pour chaque type de chambre

### 📅 Réservations
- Réservation de chambres avec sélection de dates
- Vérification de disponibilité en temps réel
- Calcul automatique du prix total
- Confirmation immédiate

### 👤 Espace Client
- Historique des réservations
- Modification des réservations existantes
- Annulation de réservations

### 👨‍💼 Administration
- Interface de gestion des chambres
- Aperçu de toutes les réservations
- Modification des statuts de réservation

## 🎨 Interface Utilisateur
- Design moderne inspiré de tunisiebook.com
- Animations et transitions fluides
- Interface entièrement responsive
- Thème visuel cohérent et attrayant
- Composants interactifs (carrousel, formulaires dynamiques)

## 🖼️ Aperçu

<div align="center">
  <img src="assets/images/screenshot1.png" alt="Page d'accueil" width="80%">
  <p><em>Page d'accueil avec carrousel de chambres</em></p>
  
  <img src="assets/images/screenshot2.png" alt="Page de réservation" width="80%">
  <p><em>Interface de réservation de chambre</em></p>
</div>

## 🛠️ Technologies Utilisées

- **Backend**: PHP 7.4+, MySQL/PDO
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Librairies**: Font Awesome, jQuery
- **Sécurité**: Protection contre les injections SQL, validation des entrées, hachage des mots de passe

## 📦 Installation

1. Clonez ce dépôt
```bash
git clone https://github.com/khouloudlassoued-sys/Php_project.git
```

2. Importez la base de données
```bash
mysql -u username -p < database.sql
```

3. Configurez la connexion à la base de données dans `config/db.php`
```php
private $host = "localhost";
private $db_name = "hotel_reservation";
private $username = "votre_username";
private $password = "votre_password";
```

4. Démarrez votre serveur web et accédez à l'application via un navigateur

## 👥 Utilisateurs par défaut

- **Admin**: 
  - Username: admin
  - Password: password
- **Client**: 
  - Username: client
  - Password: password

## 📱 Responsive Design

L'application est entièrement responsive et s'adapte à tous les appareils:
- Ordinateurs de bureau
- Tablettes
- Smartphones

## 📄 Licence

Ce projet est sous licence [MIT](LICENSE).

## 👩‍💻 Auteur

Khouloud LASSOUED

---

<p align="center">
  <a href="https://github.com/khouloudlassoued-sys">
    <img src="https://img.shields.io/badge/GitHub-100000?style=for-the-badge&logo=github&logoColor=white" alt="GitHub">
  </a>
</p> 