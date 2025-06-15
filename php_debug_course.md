# Débogage Professionnel en PHP 

---

## Table des matières

1. [Introduction : Pourquoi abandonner var_dump ?](#introduction)
2. [Le linting PHP : votre premier allié](#linting)
3. [Configuration de VSCode pour PHP](#vscode-setup)
4. [Xdebug : le debugger professionnel](#xdebug-intro)
5. [Installation et configuration d'Xdebug](#xdebug-install)
6. [Utilisation d'Xdebug dans VSCode](#xdebug-usage)
7. [Techniques avancées de débogage](#advanced-techniques)
8. [Exercices pratiques](#exercises)
9. [Récapitulatif et bonnes pratiques](#conclusion)

---

## 1. Introduction : Pourquoi abandonner var_dump ?

### Le problème avec var_dump()

Combien de fois avez-vous fait cela ?

```php
<?php
function calculateTotal($items) {
    $total = 0;
    var_dump($items); // 😱 Debug "rapide"
    
    foreach ($items as $item) {
        var_dump($item); // 😱 Encore du var_dump
        $total += $item['price'] * $item['quantity'];
    }
    
    var_dump($total); // 😱 Et encore...
    return $total;
}
```

**Problèmes de cette approche :**
- Pollution du code
- Oubli de supprimer les var_dump en production
- Pas de contrôle fin sur l'exécution
- Impossibilité d'inspecter l'état complet de l'application
- Perte de temps à ajouter/supprimer constamment du code

### L'approche professionnelle

Avec les outils modernes, le même débogage devient :
- **Non-intrusif** : pas de modification du code
- **Interactif** : inspection en temps réel
- **Précis** : breakpoints conditionnels
- **Complet** : pile d'appels, variables, contexte

---

## 2. Le linting PHP : votre premier allié {#linting}

### Qu'est-ce que le linting ?

Le linting vérifie la syntaxe de votre code **avant** l'exécution. C'est votre première ligne de défense contre les erreurs.

### Configuration de la variable d'environement PHP
Avant d'utiliser php -l, assurez-vous que PHP est accessible depuis le terminal VSCode.

#### Vérification de l'installation PHP
Ouvrez le terminal intégré VSCode (Ctrl+`` ou Cmd+`` sur Mac) et testez :

```bash
php --version
```
Si vous obtenez une erreur "command not found" ou "n'est pas reconnu", PHP n'est pas dans votre PATH.

#### Configuration du PATH selon votre OS
##### Windows :

- Localisez votre installation PHP (ex: C:\php ou C:\wamp\php)
- Ajoutez le chemin aux variables d'environnement :
- Ouvrez "Paramètres système avancés"
- Cliquez sur "Variables d'environnement"
- Modifiez la variable "Path"
- Ajoutez le chemin vers PHP



##### macOS (avec Homebrew) :
```bash
# Installation si nécessaire
brew install php
```

##### macOS (utilisé le binaire php de MAMP) :

- Ouvrer le fichier ~/.zshenv 
-  Coller ses lignes 
```bash
export MAMP_PHP=/Applications/MAMP/bin/php/php8.3.14/bin # Attention à bien choisir la bonne version php, la mienne est 8.3.14
export PATH="$MAMP_PHP:$PATH
```

#### Test de la configuration
Redémarrez VSCode et testez dans le terminal intégré :
```bash
php --version
php -m | grep -i xdebug  # Vérifier Xdebug plus tard
```

### Utilisation de php -l

La commande `php -l` (lint) vérifie la syntaxe sans exécuter le code.

**Syntaxe :**
```bash
php -l fichier.php
```

### Exemple pratique

Créons un fichier avec des erreurs :

```php
<?php
// fichier: exemple_erreur.php
function calculer($a, $b) {
    if ($a > 0 {  // ← Erreur : parenthèse manquante
        return $a + $b;
    }
    return 0;
} // ← Erreur : accolade fermante manquante
```

**Test avec php -l :**
```bash
$ php -l exemple_erreur.php
PHP Parse error: syntax error, unexpected '{' in exemple_erreur.php on line 3
```

### Exercice pratique 1

**Trouvez et corrigez les erreurs dans ce code :**

```php
<?php
// fichier: exercice1.php
class UserManager {
    private $users = [];
    
    public function addUser($name, $email) {
        if (empty($name) {
            throw new InvalidArgumentException("Le nom ne peut pas être vide");
        }
        
        $this->users[] = [
            'name' => $name,
            'email' => $email
        ];
    }
    
    public function getUsers() {
        return $this->users;
    
    public function getUserByEmail($email) {
        foreach ($this->users as $user) {
            if ($user['email'] === $email) {
                return $user;
            }
        }
        return null;
    }
```

---

## 3. Extensions VSCode pour PHP

### Extensions essentielles

1. **PHP Intelephense** - Autocomplétion et analyse statique
2. **PHP Debug** - Interface pour Xdebug
3. **PHP DocBlocker** - Génération automatique de documentation


### Linting automatique dans VSCode

Avec Intelephense, les erreurs de syntaxe apparaissent en temps réel :

```php
<?php
// Les erreurs apparaîtront avec des soulignements rouges
function test() {
    $variable = "test"  // ← Erreur : point-virgule manquant
    return $variable;
} // VSCode vous montrera l'erreur immédiatement !
```

---

## 4. Xdebug : le debugger professionnel

### Qu'est-ce que Xdebug ?

Xdebug est une extension PHP qui permet :
- Le débogage pas à pas
- Le profilage de performance
- L'analyse de couverture de code
- La trace des appels de fonctions

### Pourquoi Xdebug > var_dump ?

| var_dump             | Xdebug                   |
| -------------------- | ------------------------ |
| Modifie le code      | Non-intrusif             |
| Output statique      | Inspection interactive   |
| Pollution            | Code propre              |
| Risque en production | Sécurisé                 |
| Limité               | Fonctionnalités avancées |

---

## 5. Installation et configuration d'Xdebug

Xdebug est généralement déjà présent dans la plus part des outils et des serveurs web, mais il n'est pas toujours activé.

### Configuration dans php.ini
Décommenter cette ligne si elle est présente : 

```bash
# Supprimer le ; pour décommenter
# Attention ici c'est un path sur MAC, sur windows vous aurez quelque chose du genre : 
# ;zend_extension="c:/wamp64/bin/php/php8.3.14/zend_ext/php_xdebug-3.0.0-3.0-vc14-x86_64.dll"
;zend_extension="/Applications/MAMP/bin/php/php8.3.14/lib/php/extensions/no-debug-non-zts-20230831/xdebug.so""
```

Puis, Ajoutez ces lignes à votre `php.ini` :

```ini
; Configuration Xdebug 3.x
xdebug.mode=debug
xdebug.start_with_request=yes
```

### Vérification de l'installation

```php
<?php
$title = "Vérification de l'installation de Xdebug";
echo "<h1>$title</h1>\n";
// test_xdebug.php
if (extension_loaded('xdebug')) {
    echo "Xdebug est installé !\n";
    echo "Version : " . phpversion('xdebug') . "\n";
    echo "Mode : " . ini_get('xdebug.mode') . "\n";
} else {
    echo "Xdebug n'est pas installé.\n";
}
```

```bash
php test_xdebug.php
```

---

## 6. Utilisation d'Xdebug dans VSCode

Dans l'onglet "Run adn Debug", sélectionner "Listen for Xdebug" ou "PHP" selon la version et l'OS.

Cela générera un fichier de config, pour connecter Xdebug à votre PHP.

![alt text](image-2.png)


### Premier exemple de débogage

Créons un exemple simple à déboguer :

```php
<?php
// debug_example.php
class Calculator {
    private $history = [];
    
    public function add($a, $b) {
        $result = $a + $b;
        $this->history[] = "Addition: $a + $b = $result";
        return $result;
    }
    
    public function multiply($a, $b) {
        $result = $a * $b;
        $this->history[] = "Multiplication: $a * $b = $result";
        return $result;
    }
    
    public function getHistory() {
        return $this->history;
    }
}

$calc = new Calculator();
$result1 = $calc->add(5, 3);
$result2 = $calc->multiply($result1, 2);

echo "Résultat final: $result2\n";
echo "Historique:\n";
foreach ($calc->getHistory() as $entry) {
    echo "- $entry\n";
}
```

### Utilisation des breakpoints

1. **Placer un breakpoint :** Cliquez dans la marge à gauche du numéro de ligne
2. **Démarrer le débogage :** F5 ou menu Debug > Start Debugging
3. **Exécuter le script :** L'exécution s'arrêtera au breakpoint

### Navigation pendant le débogage

- **F10** : Step Over (ligne suivante)
- **F11** : Step Into (entrer dans la fonction)
- **Shift+F11** : Step Out (sortir de la fonction)
- **F5** : Continue (jusqu'au prochain breakpoint)

### Inspection des variables

Dans le panneau de débogage, vous pouvez :
- Voir toutes les variables locales
- Surveiller des expressions spécifiques
- Examiner la pile d'appels
- Modifier les valeurs en temps réel

### Vidéo de démonstration

![alt text](gif.gif)

---

## 7. Techniques avancées de débogage 

### Breakpoints conditionnels

Clic droit sur un breakpoint pour ajouter une condition :

```php
<?php
// Ce breakpoint ne s'activera que si $i === 50
for ($i = 0; $i < 100; $i++) {
    $result = complexCalculation($i); // Breakpoint conditionnel ici
    echo $result;
}
```

### Logpoints

Alternative aux var_dump sans modifier le code :

```php
<?php
function processUser($user) {
    // Logpoint : affiche la valeur sans arrêter l'exécution
    $validated = validateUser($user);
    return $validated;
}
```

---

## 8. Exercices pratiques {#exercises}

### Exercice 2 : Débogage d'un système de panier

Voici un code avec plusieurs bugs. Utilisez Xdebug pour les identifier et les corriger :

```php
<?php
// shopping_cart.php
class Product {
    public $id;
    public $name;
    public $price;
    public $stock;
    
    public function __construct($id, $name, $price, $stock) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
    }
}

class ShoppingCart {
    private $items = [];
    private $products = [];
    
    public function __construct() {
        // Produits de démonstration
        $this->products = [
            1 => new Product(1, 'Laptop', 999.99, 5),
            2 => new Product(2, 'Mouse', 29.99, 20),
            3 => new Product(3, 'Keyboard', 79.99, 15)
        ];
    }
    
    public function addItem($productId, $quantity) {
        if (!isset($this->products[$productId])) {
            throw new Exception("Produit inexistant");
        }
        
        $product = $this->products[$productId];

        if ($quantity >= $product->stock) {
            throw new Exception("Stock insuffisant");
        }
        
        if (isset($this->items[$productId])) {
            $this->items[$productId] += $quantity;
        } else {
            $this->items[$productId] = $quantity;
        }

        $product->stock += $quantity;
    }
    
    public function removeItem($productId, $quantity = null) {
        if (!isset($this->items[$productId])) {
            return;
        }
        
        if ($quantity === null) {
            unset($this->items[$productId]);
        } else {
            $this->items[$productId] -= $quantity;
            // Bug 3 : Gestion incorrecte des quantités négatives
            if ($this->items[$productId] < 0) {
                $this->items[$productId] = 0;
            }
        }
    }
    
    public function getTotal() {
        $total = 0;
        foreach ($this->items as $productId => $quantity) {
            $product = $this->products[$productId];
            // Bug 4 : Calcul incorrect du total
            $total += $product->price * $quantity * 1.2; // TVA ?
        }
        return $total;
    }
    
    public function getItems() {
        return $this->items;
    }
    
    public function getProducts() {
        return $this->products;
    }
}

// Test du système
try {
    $cart = new ShoppingCart();
    
    echo "Ajout de 2 laptops...\n";
    $cart->addItem(1, 2);
    
    echo "Ajout de 1 souris...\n";
    $cart->addItem(2, 1);
    
    echo "Contenu du panier:\n";
    foreach ($cart->getItems() as $productId => $quantity) {
        $product = $cart->getProducts()[$productId];
        echo "- {$product->name}: {$quantity} x {$product->price}€\n";
    }
    
    echo "Total: " . $cart->getTotal() . "€\n";
    
    echo "Stocks restants:\n";
    foreach ($cart->getProducts() as $product) {
        echo "- {$product->name}: {$product->stock} en stock\n";
    }
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
```

**Instructions :**
1. Placez des breakpoints aux lignes clés
2. Exécutez le code pas à pas
3. Identifiez les 4 bugs
4. Corrigez-les