<?php
// shopping_cart.php
class Product
{
    public $id;
    public $name;
    public $price;
    public $stock;

    public function __construct($id, $name, $price, $stock)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
    }
}

class ShoppingCart
{
    private $items = [];
    private $products = [];

    public function __construct()
    {
        // Produits de démonstration
        $this->products = [
            1 => new Product(1, 'Laptop', 999.99, 5),
            2 => new Product(2, 'Mouse', 29.99, 20),
            3 => new Product(3, 'Keyboard', 79.99, 15)
        ];
    }

    public function addItem($productId, $quantity)
    {
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

    public function removeItem($productId, $quantity = null)
    {
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

    public function getTotal()
    {
        $total = 0;
        foreach ($this->items as $productId => $quantity) {
            $product = $this->products[$productId];
            // Bug 4 : Calcul incorrect du total
            $total += $product->price * $quantity * 1.2; // TVA ?
        }
        return $total;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function getProducts()
    {
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