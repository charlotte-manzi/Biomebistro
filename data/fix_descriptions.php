<?php
require_once __DIR__ . '/../vendor/autoload.php';

use BiomeBistro\Config\Database;

$db = Database::getDatabase();

// Descriptions par restaurant
$descriptions = [
    'Canopy Dreams Café' => "Niché au cœur d'une canopée tropicale recréée, ce café offre une expérience immersive entourée de végétation luxuriante et de sons de la jungle.",
    'Jungle Paradise' => "Un voyage culinaire au cœur de la forêt tropicale avec des saveurs exotiques et une ambiance mystique.",
    'Sahara Sunset Lounge' => "Vivez la magie du désert avec une cuisine inspirée des oasis et une atmosphère chaleureuse sous un ciel étoilé.",
    'Dune & Spice' => "Une fusion de saveurs épicées du désert dans un décor de dunes de sable dorées.",
    'Ocean\'s Whisper' => "Plongez dans les profondeurs marines avec une cuisine de fruits de mer dans un décor corallien enchanteur.",
    'Coral Delights' => "Découvrez les trésors de l'océan dans une ambiance sous-marine magique et colorée.",
    'Peak Bistro' => "Savourez des plats alpins authentiques avec une vue panoramique sur les sommets enneigés.",
    'Alpine Hearth' => "Réchauffez-vous près du feu avec des spécialités de montagne dans un chalet cosy.",
    'Aurora Table' => "Dînez sous les aurores boréales dans une atmosphère glaciale et mystérieuse de l'Arctique.",
    'Frost & Flame' => "Contrastes de glace et de chaleur dans ce restaurant unique inspiré de la toundra arctique.",
    'Woodland Feast' => "Une escapade forestière avec des plats préparés à partir d'ingrédients locaux dans une ambiance sylvestre.",
    'Forest Haven' => "Refuge paisible au milieu des arbres centenaires, offrant une cuisine réconfortante et naturelle.",
    'Savanna Grill' => "Grillades africaines et saveurs sauvages dans un décor de savane à perte de vue.",
    'Sunset Plains' => "Admirez le coucher de soleil sur la plaine tout en dégustant des spécialités inspirées de l'Afrique.",
    'Funghi Fantasy' => "Entrez dans un monde mystique de champignons géants et de lumières féeriques avec une cuisine unique.",
    'Enchanted Spore' => "Découvrez la magie d'une forêt de champignons enchantée avec des plats innovants et créatifs."
];

// Mettre à jour chaque restaurant
foreach ($descriptions as $name => $description) {
    $result = $db->restaurants->updateOne(
        ['name' => $name],
        ['$set' => ['description' => $description]]
    );
    
    if ($result->getModifiedCount() > 0) {
        echo "✅ Mis à jour : $name\n";
    }
}

echo "\n🎉 Descriptions mises à jour !\n";