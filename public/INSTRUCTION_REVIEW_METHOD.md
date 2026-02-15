# INSTRUCTION : Ajouter la méthode getByEmail au modèle Review

## Fichier à modifier : src/models/Review.php

Ajoute cette méthode dans la classe Review :

```php
/**
 * Récupère tous les avis d'un utilisateur par email
 * Get all reviews for a user by email
 * 
 * @param string $email Email du reviewer
 * @return array Liste des avis
 */
public function getByEmail(string $email): array
{
    return $this->collection->find([
        'reviewer_email' => $email
    ], [
        'sort' => ['created_at' => -1]
    ])->toArray();
}
```

## Emplacement : 
Ajoute cette méthode APRÈS la méthode `getByRestaurant()` dans le fichier.

---

## VÉRIFICATION

Pour vérifier que la méthode existe déjà, cherche (Ctrl+F) : `getByEmail`

- Si elle existe déjà → Parfait, ne fais rien !
- Si elle n'existe pas → Ajoute-la comme ci-dessus
