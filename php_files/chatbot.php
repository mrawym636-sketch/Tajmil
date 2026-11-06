<?php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true);
$message = strtolower(trim($data['message'] ?? ''));
$user_name = $_SESSION['user_nom'] ?? 'chérie';

if (empty($message)) {
    echo json_encode(['reply' => "Je n'ai pas bien compris. Pouvez-vous reformuler ? 😊"]);
    exit();
}

// ========== BASE DE CONNAISSANCES DU CHATBOT (FR + DARIJA) ==========
$responses = [
    // ========== SALUTATIONS ==========
    'bonjour' => "Bonjour $user_name ! 🌸 Bienvenue chez **Tajmil Lwajh**. Comment puis-je vous aider aujourd'hui ?",
    'salut' => "Salut $user_name ! ✨ Ravi de vous voir. Que puis-je faire pour vous ?",
    'bonsoir' => "Bonsoir $user_name ! 🌙 Comment puis-je vous aider ce soir ?",
    'coucou' => "Coucou $user_name ! 💕 Comment allez-vous aujourd'hui ?",
    'hello' => "Hello $user_name ! 🌺 Bienvenue chez Tajmil Lwajh. Comment puis-je vous aider ?",
    'salam' => "Salam $user_name ! 🌸 Kifach n9dar n3awnek lyoum ?",
    'salamo 3likom' => "Wa 3likom salam $user_name ! 💕 Marhba bik chez Tajmil Lwajh !",
    'labas' => "Labas l7amdollah $user_name ! 😊 Nta/nti labas ? Kifach n9dar n3awnek ?",
    'kidayr' => "Labas $user_name ! 😊 Nta/nti kidayr ? Chno bghiti t3ref ?",
    'kifach' => "Kifach n9dar n3awnek $user_name ? 😊 Produits, prix, livraison, contact... ?",
    
    // ========== REMERCIEMENTS ==========
    'merci' => "Avec plaisir $user_name ! 💖 N'hésitez pas si vous avez d'autres questions.",
    'merci beaucoup' => "Je vous en prie $user_name ! 🌷 C'est un plaisir de vous aider.",
    'chokran' => "Afwan $user_name ! 💖 N9dar n3awnek b chi haja okhra ?",
    'chokran bzaf' => "Afwan bzaf $user_name ! 🥰 Ana hna dima !",
    'chokran bezaf' => "Afwan bzaf $user_name ! 🥰 Ana hna dima !",
    'barak llah' => "Wa fik baraka $user_name ! 🌸",
    
    // ========== AU REVOIR ==========
    'au revoir' => "Au revoir $user_name ! 👋 À bientôt chez Tajmil Lwajh !",
    'bye' => "Bye $user_name ! 🌸 Revenez vite nous voir !",
    'à bientôt' => "À bientôt $user_name ! ✨ Prenez soin de vous !",
    'bslama' => "Bslama $user_name ! 👋 Nchoufek 3la khir !",
    'beslama' => "Beslama $user_name ! 👋 Nchoufek 3la khir !",
    
    // ========== PRODUITS ==========
    'produit' => "Nous proposons une large gamme de produits beauté : crèmes hydratantes, sérums, masques, maquillage... 💄 Consultez notre page **Produits** pour découvrir toute la collection !",
    'produits' => "Découvrez nos produits cosmétiques premium sur la page **Produits** ! 💄 Nous avons des soins visage, du maquillage et bien plus encore.",
    'crème' => "Nos crèmes hydratantes sont formulées avec des ingrédients naturels pour nourrir et protéger votre peau. 🌿 Découvrez-les sur notre page Produits !",
    'creme' => "Nos crèmes hydratantes sont formulées avec des ingrédients naturels pour nourrir et protéger votre peau. 🌿 Découvrez-les sur notre page Produits !",
    'sérum' => "Nos sérums concentrés en actifs naturels illuminent et raffermissent la peau. ✨ Le sérum Vitamine C est l'un de nos best-sellers !",
    'serum' => "Nos sérums concentrés en actifs naturels illuminent et raffermissent la peau. ✨ Le sérum Vitamine C est l'un de nos best-sellers !",
    'maquillage' => "Nous avons du maquillage professionnel : fond de teint, mascara, rouge à lèvres... 💋 Tout pour sublimer votre beauté naturelle !",
    'masque' => "Nos masques visage à l'argile purifient et adoucissent la peau. 🧖‍♀️ Parfait pour un moment de détente !",
    'rouge à lèvres' => "Nos rouges à lèvres mat longue tenue sont disponibles en plusieurs teintes. 💄 Quelle couleur préférez-vous ?",
    'mascara' => "Notre **Mascara Volume Extrême** est waterproof et donne un volume incroyable ! 💋 95 DH seulement.",
    'fond de teint' => "Notre **Fond de Teint Haute Tenue** offre une couvrance modulable et un fini naturel. 💄 220 DH.",
    'vitamine c' => "Le **Sérum Vitamine C** est notre best-seller ! ✨ Il illumine, raffermit et protège la peau. 380 DH.",
    'argile' => "Notre **Masque Argile Rose** purifie et adoucit la peau. 🌸 Parfait 1x/semaine ! 180 DH.",
    'huile' => "L'**Huile Démaquillante Douceur** transforme votre routine nettoyage en pur moment de détente. 💧",
    'produit jdid' => "Bientôt de nouvelles arrivées ! 🌟 Suivez-nous sur les réseaux pour ne rien rater.",
    'nouveauté' => "De nouvelles nouveautés arrivent bientôt ! 🌟 Restez connectée !",
    
    // ========== PRIX ==========
    'prix' => "Nos prix varient de **95 DH à 450 DH** selon les produits. 💰 Consultez la page Produits pour voir tous les tarifs !",
    'combien' => "Les prix commencent à **95 DH** pour le mascara et vont jusqu'à **450 DH** pour nos sérums premium. 💸",
    'cher' => "Nous proposons des produits de qualité à des prix accessibles ! 💕 Et n'oubliez pas de consulter nos promos régulières !",
    'ch7al' => "Les prix kaybda mn **95 DH** hta l **450 DH** 💰 Chof la page Produits bach tchof kolchi !",
    'bch7al' => "Les prix kaybda mn **95 DH** hta l **450 DH** 💰 Chof la page Produits bach tchof kolchi !",
    'taman' => "Les prix kaybda mn **95 DH** hta l **450 DH** 💰 Chof la page Produits bach tchof kolchi !",
    'ghali' => "Les prix mzyanin $user_name ! 💕 Kolchi qualité 3aliya b taman mzyan !",
    
    // ========== LIVRAISON ==========
    'livraison' => "🚚 **Livraison gratuite** à partir de 500 DH d'achat ! Sinon, frais de livraison de 30 DH partout au Maroc. Livraison en 24-48h.",
    'livrer' => "Nous livrons partout au Maroc en **24-48h**. 🚚 Livraison gratuite dès 500 DH d'achat !",
    'combien de temps' => "La livraison prend généralement **24 à 48h ouvrables**. ⏱️ Vous recevrez un SMS de confirmation.",
    'délai' => "Nos délais de livraison sont de **24-48h** pour tout le Maroc. 📦 C'est rapide !",
    'tawsil' => "Tawsil kaykhod **24-48h** f kol l Maghrib. 🚚 Gratuit mn 500 DH !",
    'ch7al mn wa9t' => "Tawsil kaykhod **24-48h** ouvrables. ⏱️ Ghadi tji lik SMS b confirmation !",
    'fin katwslo' => "Kanwslo f **kol l Maghrib** ! 🇲🇦 24-48h w khalas.",
    
    // ========== PAIEMENT ==========
    'paiement' => "💳 Nous acceptons : **Paiement à la livraison** (cash), virement bancaire et paiement en ligne sécurisé.",
    'payer' => "Vous pouvez payer à la livraison (cash), par virement ou en ligne. 💰 C'est vous qui choisissez !",
    'carte' => "Oui, nous acceptons les cartes bancaires pour le paiement en ligne sécurisé. 💳",
    'khalas' => "T9dar tkhalas **cash 3la tawsil**, virement, wla en ligne. 💰 Kifma bghiti !",
    'cash' => "Oui ! Paiement cash à la livraison disponible. 💵 C'est simple et sécurisé !",
    
    // ========== RETOURS ==========
    'retour' => "🔄 Vous disposez de **7 jours** pour retourner un produit non ouvert. Contactez-nous pour plus d'infos.",
    'remboursement' => "Le remboursement est effectué sous **5-7 jours** après réception du retour. 💸",
    'échange' => "Oui, vous pouvez échanger un produit dans les **7 jours** suivant l'achat. 🔄",
    'rjo3' => "T9dar trj3 l produit f **7 ayyam** ila ma t7elch. 🔄 3ayt lina bach n3awnok !",
    
    // ========== CONTACT ==========
    'contact' => "📞 Vous pouvez nous joindre :\n• Téléphone/WhatsApp : **+212 723-699031**\n• Email : **contact@tajmil.ma**\n• Adresse : Casablanca, Maroc",
    'téléphone' => "Notre numéro : **+212 723-699031** 📱 (aussi disponible sur WhatsApp)",
    'telephone' => "Notre numéro : **+212 723-699031** 📱 (aussi disponible sur WhatsApp)",
    'email' => "Notre email : **contact@tajmil.ma** 📧 Nous répondons sous 24h !",
    'adresse' => "Nous sommes situés à **Casablanca, Maroc** 📍 Consultez la page Contact pour la carte !",
    'whatsapp' => "Contactez-nous sur WhatsApp : **+212 723-699031** 📱 Nous répondons rapidement !",
    'horaires' => "Nos horaires : **Lundi - Samedi : 09:00 - 20:00** 🕐 Dimanche : Fermé",
    'fin' => "Kaynin f **Casablanca, Maroc** 📍 Chof la page Contact bach tchof la carte !",
    'fin kaynin' => "Kaynin f **Casablanca, Maroc** 📍 Chof la page Contact bach tchof la carte !",
    'numero' => "Numéro dyalna : **+212 723-699031** 📱 WhatsApp disponible !",
    'ch7al numero' => "Numéro dyalna : **+212 723-699031** 📱 WhatsApp disponible !",
    
    // ========== COMPTE ==========
    'compte' => "Créez votre compte gratuitement pour suivre vos commandes, gérer vos favoris et profiter d'offres exclusives ! 👤",
    'inscription' => "L'inscription est **gratuite et rapide** ! Cliquez sur **Se connecter** puis **Créer un compte**. ✨",
    'connexion' => "Connectez-vous avec votre email et mot de passe. Si vous n'avez pas de compte, créez-en un gratuitement ! 🔐",
    'mot de passe' => "Si vous avez oublié votre mot de passe, contactez-nous à contact@tajmil.ma 🔑",
    'mot de passe oublié' => "Contactez-nous à contact@tajmil.ma pour réinitialiser votre mot de passe 🔑",
    'inscription' => "L'inscription est gratuite ! Cliquez sur 'Se connecter' puis 'Créer un compte'. ✨",
    'compte jdid' => "Créez votre compte gratuitement ! ✨ Cliquez sur 'Se connecter' puis 'Créer un compte'.",
    
    // ========== COMMANDES ==========
    'commande' => "📦 Suivez vos commandes dans **Mes commandes** (accessible depuis votre profil). Vous recevrez aussi des notifications !",
    'suivi' => "Suivez votre commande dans la section **Mes commandes** de votre compte. 📦 Vous recevrez un SMS avec le numéro de suivi !",
    'annuler' => "Pour annuler une commande, contactez-nous rapidement au **+212 723-699031**. ⏰ Avant l'expédition !",
    'acheter' => "Super $user_name ! 🛍️ Parcourez notre page **Produits**, ajoutez au panier, puis cliquez sur **Passer commande**. Simple et rapide !",
    'commander' => "Pour commander :\n1. Choisissez vos produits 🛍️\n2. Ajoutez au panier 🛒\n3. Cliquez sur 'Passer commande' ✅\n4. Remplissez vos infos\n5. C'est commandé ! 🎉",
    'stock' => "Tous nos produits affichés sont en stock ! ✅ Si un produit est indisponible, contactez-nous.",
    'commande dyaly' => "Chof 'Mes commandes' f compte dyalek bach tchof l commandes dyalek. 📦",
    'bghit ncommandi' => "Parcourez la page **Produits**, ajoutez au panier 🛒 puis cliquez sur **Passer commande** ! Simple et rapide 😊",
    
    // ========== FAVORIS & PANIER ==========
    'favoris' => "Cliquez sur le cœur ❤️ d'un produit pour l'ajouter à vos favoris ! Retrouvez-les dans **Mes favoris**.",
    'panier' => "Ajoutez des produits au panier 🛒 puis cliquez sur **Passer commande** pour finaliser votre achat.",
    'wishlist' => "Votre liste de souhaits vous permet de sauvegarder vos produits préférés. ❤️ Cliquez sur le cœur !",
    'favoris dyaly' => "Cliquez sur le cœur ❤️ d'un produit, w ghadi ytsjjel f 'Mes favoris' !",
    
    // ========== PROMOTIONS ==========
    'promo' => "🎉 Profitez de **-30%** sur votre première commande ! Utilisez le code **BIENVENUE30**. Offre limitée !",
    'réduction' => "Code promo **BIENVENUE30** pour -30% sur votre première commande ! 🎁 N'attendez pas !",
    'solde' => "Consultez régulièrement notre page Produits pour voir les promotions en cours ! 🏷️",
    'cadeau' => "🎁 Offrez un **coffret cadeau** Tajmil Lwajh ! Emballage luxueux offert. Parfait pour faire plaisir !",
    'offrir' => "Nos coffrets cadeaux sont parfaits pour les occasions spéciales. 🎁 Emballage élégant offert !",
    'promo jdida' => "Profitez de **-30%** avec le code **BIENVENUE30** ! 🎉 Offre limitée !",
    'code promo' => "Utilisez le code **BIENVENUE30** pour -30% sur votre première commande ! 🎁",
    
    // ========== CONSEILS BEAUTÉ ==========
    'conseil' => "💡 Nos conseils beauté :\n• Hydratez votre peau matin et soir\n• Utilisez un sérum Vitamine C le matin\n• Masque argile 1x/semaine\n• Protégez-vous du soleil !",
    'routine' => "🌿 Routine beauté idéale :\n1. Nettoyage doux\n2. Sérum Vitamine C\n3. Crème hydratante\n4. Masque 1x/semaine\n5. Protection solaire !",
    'peau sèche' => "Pour peau sèche, je recommande notre **Crème Hydratante 24H** et l'**Huile Démaquillante Douceur**. 💧",
    'peau grasse' => "Pour peau grasse, optez pour le **Masque Argile Rose** et le **Sérum Vitamine C**. 🌸",
    'peau sensible' => "Pour peau sensible, privilégiez nos produits doux à base de plantes. 🌸 La Crème Hydratante 24H est parfaite !",
    'acné' => "Nos produits à l'argile et au zinc sont parfaits pour les peaux à imperfections. Consultez la page Produits ! 🌿",
    'naturel' => "🌿 Oui, nos produits sont formulés avec des ingrédients naturels et **non testés sur les animaux** !",
    'ingrédient' => "Nos produits contiennent des ingrédients naturels : argile, vitamines, huiles essentielles... 🌿 Tous sans parabènes !",
    'bio' => "Nos produits sont naturels et respectueux de l'environnement. 🌱 Découvrez-les sur notre page Produits !",
    'vegan' => "Oui, la plupart de nos produits sont **vegan** et non testés sur les animaux ! 🐰💚",
    'âge' => "Nos produits conviennent à tous les âges et tous types de peau ! 👩‍🦰 Consultez les descriptions sur la page Produits.",
    'conseil beauté' => "💡 Routine beauté : nettoyage, sérum, crème, masque 1x/semaine, protection solaire ! 🌿",
    'skincare' => "🌿 Skincare routine : nettoyage, sérum Vitamine C, crème hydratante, masque 1x/semaine !",
    'jfaf' => "Pour peau sèche, utilisez notre **Crème Hydratante 24H** 💧 w l'**Huile Démaquillante** !",
    'peau jafa' => "Pour peau sèche : **Crème Hydratante 24H** + **Huile Démaquillante** 💧",
    '7bob' => "Nos produits à l'argile sont parfaits pour l'acné. 🌿 Chof la page Produits !",
    
    // ========== AVIS ==========
    'avis' => "⭐ Consultez les avis de nos clientes sur la page **Avis** ! Vous pouvez aussi laisser votre propre avis après un achat.",
    'note' => "Nos produits sont notés en moyenne **4.8/5** par nos clientes ! ⭐ Merci pour leur confiance.",
    'témoignage' => "Lisez les témoignages de nos clientes satisfaites sur la page **Avis**. 💕",
    'avis dyaly' => "Chof 'Avis' bach tchof les avis dyal les clientes ! ⭐ T9dar tzid avis dyalek !",
    
    // ========== À PROPOS ==========
    'qui êtes-vous' => "Tajmil Lwajh est une marque marocaine de cosmétiques naturels fondée en 2024. 🌸 Notre mission : révéler la beauté naturelle de chaque femme !",
    'tajmil' => "Tajmil Lwajh signifie 'embellir le visage' en arabe. ✨ Nous sublimons votre beauté avec des produits naturels et de qualité !",
    'marque' => "Tajmil Lwajh est une marque marocaine de cosmétiques naturels. 🌿 Découvrez notre histoire sur la page **À propos** !",
    'équipe' => "Notre équipe passionnée travaille chaque jour pour vous offrir le meilleur de la beauté naturelle ! 👩‍🔬",
    'chkoun ntoma' => "Tajmil Lwajh hiya marque marocaine de cosmétiques naturels. 🌸 Mission dyalna : nbiyno jamal dyalek !",
    'histoire' => "Tajmil Lwajh fondée en 2024 par des passionnés de beauté. 🌸 Découvrez notre histoire sur la page **À propos** !",
    
    // ========== AIDE ==========
    'aide' => "🤝 Je peux vous aider avec :\n• Produits & prix\n• Livraison & paiement\n• Commandes & retours\n• Conseils beauté\n• Contact\n\nQue souhaitez-vous savoir ?",
    'help' => "🤝 Je suis là pour vous aider ! Posez-moi vos questions sur nos produits, livraison, paiement ou conseils beauté.",
    'question' => "Posez-moi votre question ! 💬 Je connais tout sur nos produits, livraison, paiement et conseils beauté.",
    '3awnek' => "Kifach n9dar n3awnek $user_name ? 😊 Produits, prix, livraison, contact... ?",
    '3afak' => "Marhba $user_name ! 🌸 Chno bghiti t3ref ?",
    'wach' => "Wach 3andek chi so2al 3la les produits dyalna ? 💄",
    'bghit' => "Bghiti t3ref chno $user_name ? Produits, prix, livraison, contact... ?",
    
    // ========== POSITIF ==========
    'super' => "Merci $user_name ! 🌟 Vous êtes géniale !",
    'génial' => "Merci beaucoup $user_name ! 💖 C'est un plaisir de vous aider !",
    'parfait' => "Parfait $user_name ! ✨ N'hésitez pas si vous avez d'autres questions.",
    'cool' => "Merci $user_name ! 😊 Comment puis-je vous aider davantage ?",
    'mzyan' => "Mzyan $user_name ! ✨ Chno bghiti t3ref ?",
    'mzyan bzaf' => "Mzyan bzaf $user_name ! 🥰 Chno bghiti t3ref ?",
    'zwina' => "Nta/nti li zwina $user_name ! 🌸",
    'zwine' => "Nta/nti li zwine $user_name ! 🌸",
    'ah' => "Super $user_name ! 😊 Chno bghiti t3ref ?",
    'la' => "D'accord $user_name ! 😊 Ila 7tajti chi haja, ana hna !",
    'oui' => "Super $user_name ! 😊 Y a-t-il autre chose que je peux faire pour vous ?",
    'non' => "D'accord $user_name ! 😊 N'hésitez pas si vous changez d'avis. Je suis là pour vous aider !",
    'ok' => "Parfait $user_name ! ✨ Autre chose ?",
    'd\'accord' => "D'accord $user_name ! 💕 Puis-je vous aider avec autre chose ?",
    
    // ========== NÉGATIF ==========
    'nul' => "Je suis désolée si je n'ai pas été à la hauteur $user_name. 😔 Contactez-nous au +212 723-699031 pour une aide personnalisée.",
    'problème' => "Je suis désolée pour ce problème $user_name. 😔 Contactez notre support : +212 723-699031 ou contact@tajmil.ma",
    'plainte' => "Nous prenons vos remarques très au sérieux. 🙏 Contactez-nous au +212 723-699031 pour résoudre ce problème.",
    'ma3rftch' => "Ma mochkil $user_name ! 😊 N9dar n3awnek b chi haja okhra ?",
    'mochkil' => "Sme7 liya $user_name. 😔 Contactez-nous au +212 723-699031 pour une aide personnalisée.",
    
    // ========== FUN ==========
    'blague' => "😄 Pourquoi les cosmétiques sont-ils toujours heureux ? Parce qu'ils font bonne figure ! 💄😂",
    'rire' => "😄 Ahah ! J'adore vous faire sourire $user_name ! 💕",
    'chante' => "🎵 'Tajmil Lwajh, la beauté au naturel...' 🎶 Désolée, je chante faux ! 😅",
    'je t\'aime' => "Ohhh merci $user_name ! 🥰 Moi aussi je vous aime ! Vous êtes adorable !",
    'kanbghik' => "Ohhh $user_name ! 🥰 Ana tanbghik bzaf ! Nta/nti adorable !",
    'belle' => "Merci $user_name ! 🌸 C'est vous qui êtes belle !",
    'gentil' => "Merci $user_name ! 💕 Vous êtes adorable !",
    'adore' => "Merci $user_name ! 🥰 Je suis ravie de vous plaire !",
    
    // ========== ÉMOTIONS ==========
    'triste' => "Oh non $user_name... 😔 Prenez soin de vous. Un peu de self-care avec nos produits pourrait vous faire du bien 💕",
    'fatigué' => "Reposez-vous $user_name ! 😴 Un bon masque et une crème hydratante, et vous repartez comme neuve ! 💆‍♀️",
    'stress' => "Prenez un moment pour vous $user_name ! 🧘‍♀️ Un soin visage apaise toujours. 💆‍♀️",
    'm3asba' => "Sme7 liya $user_name... 😔 Khod wa9t l rasek, self-care b chi masque w crème ghadi y3awnek 💕",
    '3ayana' => "Rte7 $user_name ! 😴 Masque + crème hydratante = kifach tji mzyana ! 💆‍♀️",
    'stressé' => "Khod wa9t l rasek $user_name ! 🧘‍♀️ Soin visage kayrta7 bzaf 💆‍♀️",
    
    // ========== EMPLOI ==========
    'emploi' => "Nous recherchons régulièrement des passionnés de beauté ! 💼 Envoyez votre CV à contact@tajmil.ma",
    'recrutement' => "Envoyez votre candidature à contact@tajmil.ma 💼 Nous serions ravis de vous rencontrer !",
    'stage' => "Nous acceptons des stagiaires motivés ! 📩 Contactez-nous à contact@tajmil.ma",
    'khedma' => "Ila bghiti tkhdem m3ana, sift CV dyalek l **contact@tajmil.ma** 💼",
    
    // ========== PARTENARIAT ==========
    'partenariat' => "Pour un partenariat, contactez-nous à contact@tajmil.ma 🤝 Nous étudions toutes les propositions !",
    'collaboration' => "Nous sommes ouverts aux collaborations ! 🤝 Écrivez-nous à contact@tajmil.ma",
    'charka' => "Ila bghiti chi charka, sift lina f **contact@tajmil.ma** 🤝",
    
    // ========== RÉSEAUX SOCIAUX ==========
    'instagram' => "Suivez-nous sur Instagram : **@mohamd_amroui** 📸",
    'facebook' => "Retrouvez-nous sur Facebook Messenger : **mohamd_amroui** 📘",
    'tiktok' => "Suivez-nous sur TikTok : **@mohamd_amroui** 🎵",
    'résaux' => "Suivez-nous sur Instagram, Facebook et TikTok : **@mohamd_amroui** 📱",
    
    // ========== FIDÉLITÉ ==========
    'fidélité' => "🎁 Programme fidélité : cumulez des points à chaque achat et obtenez des réductions ! Bientôt disponible...",
    'points' => "Cumulez des points fidélité avec chaque achat ! 🎯 1 DH = 1 point. 100 points = 10 DH de réduction !",
    
    // ========== NEWSLETTER ==========
    'newsletter' => "Inscrivez-vous à notre newsletter pour recevoir nos offres exclusives ! 📧 Cliquez sur le pied de page.",
    'news' => "Nos nouveautés arrivent bientôt ! 🌟 Suivez-nous sur les réseaux sociaux pour ne rien rater.",
    
    // ========== FONCTIONNEMENT ==========
    'comment ça marche' => "C'est simple $user_name ! 🌟\n1. Parcourez nos produits\n2. Ajoutez au panier\n3. Commandez\n4. Recevez en 24-48h\n5. Profitez !",
    'processus' => "Notre processus est simple : commandez, on prépare, on livre, vous profitez ! 📦✨",
    'répète' => "Bien sûr $user_name ! Je disais : comment puis-je vous aider avec nos produits beauté ? 💄",
    'comprends pas' => "Désolée $user_name, je n'ai pas compris. 😅 Pouvez-vous reformuler ? Ou tapez 'aide' pour voir ce que je sais faire !",
    'mafhemtch' => "Sme7 liya $user_name, ma fhemtch. 😅 3awd l so2al wla kteb 'aide' bach tchof chno n9dar n3awnek !",
];

// ========== RECHERCHE DE MOTS-CLÉS ==========
$reply = null;

// Recherche exacte d'abord
if (isset($responses[$message])) {
    $reply = $responses[$message];
} else {
    // Recherche par mots-clés (le message contient le mot-clé)
    foreach ($responses as $keyword => $response) {
        if (strpos($message, $keyword) !== false) {
            $reply = $response;
            break;
        }
    }
}

// ========== RÉPONSES PAR DÉFAUT ==========
if (!$reply) {
    $fallbacks = [
        "Hmm, je ne suis pas sûre de bien comprendre $user_name. 😅 Pouvez-vous reformuler ? Ou tapez '**aide**' pour voir ce que je sais faire !",
        "Je suis encore en apprentissage $user_name ! 🌸 Pour une réponse précise, contactez-nous au **+212 723-699031**.",
        "Désolée $user_name, cette question me dépasse ! 😅 Tapez '**aide**' pour découvrir ce que je sais, ou contactez notre équipe !",
        "Je ne connais pas encore la réponse à ça $user_name ! 💕 Essayez '**aide**' pour voir mes compétences, ou appelez-nous !",
        "Sme7 liya $user_name ! 😅 Ma fhemtch. 3awd l so2al wla kteb '**aide**' bach tchof chno n9dar n3awnek !",
    ];
    $reply = $fallbacks[array_rand($fallbacks)];
}

// ========== SAUVEGARDE DE LA CONVERSATION ==========
try {
    $check = $pdo->query("SHOW TABLES LIKE 'chat_logs'");
    if ($check->rowCount() == 0) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS chat_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            user_message TEXT NOT NULL,
            bot_reply TEXT NOT NULL,
            date_chat TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }
    $stmt = $pdo->prepare("INSERT INTO chat_logs (user_id, user_message, bot_reply) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'] ?? null, $message, $reply]);
} catch (PDOException $e) {
    // Silencieux
}

echo json_encode(['reply' => $reply]);