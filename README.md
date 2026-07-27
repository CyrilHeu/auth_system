<div align="center">

<h1>Laravel Firebase Authentication</h1>

<p>
  <strong>Système d'authentification Laravel utilisant Firebase Authentication REST avec prise en charge de plusieurs environnements et gestion centralisée des accès.</strong>
</p>

</div>

<hr>

<h2>Présentation</h2>

<p>
Ce projet propose une architecture d'authentification développée avec
<strong>Laravel</strong>, reposant sur l'API REST de Firebase Authentication.
</p>

<p>
Il a été conçu pour permettre l'utilisation de plusieurs projets Firebase
au sein d'une même application, avec une sélection automatique de
l'environnement (développement, production, recette, etc.) selon le compte
utilisateur connecté.
</p>

<p>
L'objectif est de centraliser la gestion des accès tout en conservant
une authentification sécurisée et totalement indépendante des SDK Firebase.
</p>

<hr>

<h2>Objectifs</h2>

<ul>

<li>centraliser la gestion des utilisateurs dans un registre SQL ;</li>

<li>sélectionner automatiquement le projet Firebase correspondant ;</li>

<li>proposer une authentification entièrement basée sur l'API REST Firebase ;</li>

<li>garantir une gestion sécurisée des sessions et des jetons ;</li>

<li>faciliter l'intégration dans des applications Laravel multi-environnements.</li>

</ul>

<hr>

<h2>Fonctionnalités</h2>

<table>

<thead>
<tr>
<th>Authentification</th>
<th>Sécurité</th>
<th>Administration</th>
</tr>
</thead>

<tbody>

<tr>
<td>Connexion Firebase REST</td>
<td>Gestion sécurisée des sessions</td>
<td>Registre SQL centralisé</td>
</tr>

<tr>
<td>Sélection dynamique du projet Firebase</td>
<td>Renouvellement automatique des ID Tokens</td>
<td>Association automatique du Firebase UID</td>
</tr>

<tr>
<td>Réinitialisation du mot de passe</td>
<td>Gestion des Refresh Tokens</td>
<td>Support multi-environnements</td>
</tr>

<tr>
<td>Option « Se souvenir de moi »</td>
<td>Expiration automatique des sessions</td>
<td>Compatibilité multi-projets Firebase</td>
</tr>

<tr>
<td>Option « Rester connecté »</td>
<td>Protection des routes Laravel</td>
<td>Configuration centralisée</td>
</tr>

</tbody>

</table>

<hr>

<h2>Technologies</h2>

<table>

<thead>
<tr>
<th>Domaine</th>
<th>Technologies</th>
</tr>
</thead>

<tbody>

<tr>
<td>Framework</td>
<td>Laravel</td>
</tr>

<tr>
<td>Authentification</td>
<td>Firebase Authentication REST API</td>
</tr>

<tr>
<td>Base de données</td>
<td>MySQL</td>
</tr>

<tr>
<td>Accès aux données</td>
<td>PDO</td>
</tr>

<tr>
<td>Langage</td>
<td>PHP 8.2+</td>
</tr>

<tr>
<td>Architecture</td>
<td>Multi-environnements</td>
</tr>

</tbody>

</table>

<hr>

<h2>Architecture</h2>

<p>
L'utilisateur est d'abord identifié dans un registre SQL centralisé afin
de déterminer le projet Firebase auquel il appartient. L'authentification
est ensuite réalisée via l'API REST Firebase avant la création d'une session
Laravel sécurisée et le renouvellement automatique des jetons si nécessaire.
</p>

<hr>

<h2>Cas d'utilisation</h2>

<ul>

<li>applications multi-clients (SaaS) ;</li>

<li>gestion des environnements de développement et de production ;</li>

<li>plateformes utilisant plusieurs projets Firebase ;</li>

<li>applications White Label ;</li>

<li>portails d'administration centralisés.</li>

</ul>

<hr>

<h2>État du développement</h2>

<p>
Ce projet constitue une base technique d'authentification réutilisable,
conçue pour être intégrée dans des applications Laravel nécessitant une
gestion centralisée des accès et une prise en charge de plusieurs projets
Firebase.
</p>

<hr>

<h2>Licence</h2>

<p>
Projet privé — Tous droits réservés.
</p>
