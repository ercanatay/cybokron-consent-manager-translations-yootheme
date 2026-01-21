<?php
/**
 * String definitions for all supported languages
 *
 * @package YT_Consent_Translations
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class YTCT_Strings
 * Contains all translation strings for supported languages
 */
class YTCT_Strings {

    /**
     * Available languages
     */
    private static $languages = [
        'auto' => 'Auto (WordPress Default)',
        'en' => 'English',
        'zh' => '中文',
        'es' => 'Español',
        'fr' => 'Français',
        'pt' => 'Português',
        'ru' => 'Русский',
        'ja' => '日本語',
        'id' => 'Bahasa Indonesia',
        'it' => 'Italiano',
        'nl' => 'Nederlands',
        'pl' => 'Polski',
        'vi' => 'Tiếng Việt',
        'th' => 'ไทย',
        'uk' => 'Українська',
        'cs' => 'Čeština',
        'el' => 'Ελληνικά',
        'ro' => 'Română',
        'hu' => 'Magyar',
        'sv' => 'Svenska',
        'da' => 'Dansk',
        'fi' => 'Suomi',
        'nb' => 'Norsk',
        'he' => 'עברית',
        'ms' => 'Bahasa Melayu',
        'bn' => 'বাংলা',
        'fa' => 'فارسی',
        'ta' => 'தமிழ்',
        'te' => 'తెలుగు',
        'mr' => 'मराठी',
        'sw' => 'Kiswahili',
        'tl' => 'Filipino',
        'tr' => 'Türkçe',
        'hi' => 'हिन्दी',
        'ko' => '한국어',
        'ar' => 'العربية',
        'de' => 'Deutsch'
    ];

    /**
     * WordPress locale to plugin language code mapping
     */
    private static $locale_map = [
        'en_US' => 'en', 'en_GB' => 'en', 'en_AU' => 'en', 'en_CA' => 'en',
        'zh_CN' => 'zh', 'zh_TW' => 'zh', 'zh_HK' => 'zh',
        'es_ES' => 'es', 'es_MX' => 'es', 'es_AR' => 'es', 'es_CO' => 'es',
        'fr_FR' => 'fr', 'fr_CA' => 'fr', 'fr_BE' => 'fr',
        'pt_BR' => 'pt', 'pt_PT' => 'pt',
        'ru_RU' => 'ru',
        'ja' => 'ja', 'ja_JP' => 'ja',
        'id_ID' => 'id',
        'it_IT' => 'it',
        'nl_NL' => 'nl', 'nl_BE' => 'nl',
        'pl_PL' => 'pl',
        'vi' => 'vi', 'vi_VN' => 'vi',
        'th' => 'th', 'th_TH' => 'th',
        'uk' => 'uk', 'uk_UA' => 'uk',
        'cs_CZ' => 'cs',
        'el' => 'el', 'el_GR' => 'el',
        'ro_RO' => 'ro',
        'hu_HU' => 'hu',
        'sv_SE' => 'sv',
        'da_DK' => 'da',
        'fi' => 'fi', 'fi_FI' => 'fi',
        'nb_NO' => 'nb', 'nn_NO' => 'nb',
        'he_IL' => 'he',
        'ms_MY' => 'ms',
        'bn_BD' => 'bn',
        'fa_IR' => 'fa',
        'ta_IN' => 'ta', 'ta_LK' => 'ta',
        'te_IN' => 'te',
        'mr_IN' => 'mr',
        'sw' => 'sw', 'sw_KE' => 'sw',
        'tl' => 'tl', 'fil' => 'tl',
        'tr_TR' => 'tr',
        'hi_IN' => 'hi',
        'ko_KR' => 'ko',
        'ar' => 'ar', 'ar_SA' => 'ar', 'ar_AE' => 'ar', 'ar_EG' => 'ar',
        'de_DE' => 'de', 'de_AT' => 'de', 'de_CH' => 'de', 'de_DE_formal' => 'de'
    ];

    /**
     * String keys with their original English text
     */
    private static $string_keys = [
        'banner_text' => 'We use cookies and similar technologies to improve your experience on our website.',
        'banner_link' => 'Read our <a href="%s">Privacy Policy</a>.',
        'button_accept' => 'Accept',
        'button_reject' => 'Reject',
        'button_settings' => 'Manage Settings',
        'modal_title' => 'Privacy Settings',
        'modal_content' => 'This website uses cookies and similar technologies. They are grouped into categories, which you can review and manage below. If you have accepted any non-essential cookies, you can change your preferences at any time in the settings.',
        'modal_content_link' => 'Learn more in our <a href="%s">Privacy Policy</a>.',
        'functional_title' => 'Functional',
        'preferences_title' => 'Preferences',
        'statistics_title' => 'Statistics',
        'marketing_title' => 'Marketing',
        'functional_content' => 'These technologies are required to activate the core functionality of our website.',
        'preferences_content' => 'These technologies allow our website to remember your preferences and provide you with a more personalized experience.',
        'statistics_content' => 'These technologies enable us to analyse the use of our website in order to measure and improve performance.',
        'marketing_content' => 'These technologies are used by our marketing partners to show you personalized advertisements relevant to your interests.',
        'show_services' => 'Show Services',
        'hide_services' => 'Hide Services',
        'modal_accept' => 'Accept all',
        'modal_reject' => 'Reject all',
        'modal_save' => 'Save'
    ];

    /**
     * Get available languages
     *
     * @return array
     */
    public static function get_languages() {
        return self::$languages;
    }

    /**
     * Detect language from WordPress locale
     *
     * @return string Language code (en, tr, hi, ko, ar, de)
     */
    public static function detect_wp_language() {
        $locale = get_locale();
        
        // Direct match
        if (isset(self::$locale_map[$locale])) {
            return self::$locale_map[$locale];
        }
        
        // Try base language (e.g., 'de' from 'de_DE')
        $base_lang = substr($locale, 0, 2);
        foreach (self::$locale_map as $wp_locale => $lang_code) {
            if (strpos($wp_locale, $base_lang) === 0) {
                return $lang_code;
            }
        }
        
        // Default to English
        return 'en';
    }

    /**
     * Get locale map
     *
     * @return array
     */
    public static function get_locale_map() {
        return self::$locale_map;
    }

    /**
     * Get string keys with original text
     *
     * @return array
     */
    public static function get_string_keys() {
        return self::$string_keys;
    }

    /**
     * Get original English text by key
     *
     * @param string $key String key
     * @return string|null
     */
    public static function get_original($key) {
        return isset(self::$string_keys[$key]) ? self::$string_keys[$key] : null;
    }

    /**
     * Get translations for a specific language
     *
     * @param string $lang Language code
     * @return array
     */
    public static function get_translations($lang = 'en') {
        $translations = self::get_all_translations();
        return isset($translations[$lang]) ? $translations[$lang] : $translations['en'];
    }

    /**
     * Get all translations for all languages
     *
     * @return array
     */
    public static function get_all_translations() {
        return [
            // English (Default)
            'en' => [
                'banner_text' => 'We use cookies and similar technologies to improve your experience on our website.',
                'banner_link' => 'Read our <a href="%s">Privacy Policy</a>.',
                'button_accept' => 'Accept',
                'button_reject' => 'Reject',
                'button_settings' => 'Manage Settings',
                'modal_title' => 'Privacy Settings',
                'modal_content' => 'This website uses cookies and similar technologies. They are grouped into categories, which you can review and manage below. If you have accepted any non-essential cookies, you can change your preferences at any time in the settings.',
                'modal_content_link' => 'Learn more in our <a href="%s">Privacy Policy</a>.',
                'functional_title' => 'Functional',
                'preferences_title' => 'Preferences',
                'statistics_title' => 'Statistics',
                'marketing_title' => 'Marketing',
                'functional_content' => 'These technologies are required to activate the core functionality of our website.',
                'preferences_content' => 'These technologies allow our website to remember your preferences and provide you with a more personalized experience.',
                'statistics_content' => 'These technologies enable us to analyse the use of our website in order to measure and improve performance.',
                'marketing_content' => 'These technologies are used by our marketing partners to show you personalized advertisements relevant to your interests.',
                'show_services' => 'Show Services',
                'hide_services' => 'Hide Services',
                'modal_accept' => 'Accept all',
                'modal_reject' => 'Reject all',
                'modal_save' => 'Save'
            ],

            // Turkish
            'tr' => [
                'banner_text' => 'Web sitemizde deneyiminizi iyileştirmek için çerezler ve benzer teknolojiler kullanıyoruz.',
                'banner_link' => '<a href="%s">Gizlilik Politikamızı</a> okuyun.',
                'button_accept' => 'Kabul Et',
                'button_reject' => 'Reddet',
                'button_settings' => 'Ayarları Yönet',
                'modal_title' => 'Gizlilik Ayarları',
                'modal_content' => 'Bu web sitesi çerezler ve benzer teknolojiler kullanmaktadır. Bunlar, aşağıda inceleyip yönetebileceğiniz kategorilere ayrılmıştır. Zorunlu olmayan çerezleri kabul ettiyseniz, tercihlerinizi istediğiniz zaman ayarlardan değiştirebilirsiniz.',
                'modal_content_link' => '<a href="%s">Gizlilik Politikamızdan</a> daha fazla bilgi edinin.',
                'functional_title' => 'Fonksiyonel',
                'preferences_title' => 'Tercihler',
                'statistics_title' => 'İstatistik',
                'marketing_title' => 'Pazarlama',
                'functional_content' => 'Bu teknolojiler, web sitemizin temel işlevselliğini etkinleştirmek için gereklidir.',
                'preferences_content' => 'Bu teknolojiler, web sitemizin tercihlerinizi hatırlamasını ve size daha kişiselleştirilmiş bir deneyim sunmasını sağlar.',
                'statistics_content' => 'Bu teknolojiler, performansı ölçmek ve iyileştirmek amacıyla web sitemizin kullanımını analiz etmemizi sağlar.',
                'marketing_content' => 'Bu teknolojiler, pazarlama ortaklarımız tarafından ilgi alanlarınıza uygun kişiselleştirilmiş reklamlar göstermek için kullanılır.',
                'show_services' => 'Servisleri Göster',
                'hide_services' => 'Servisleri Gizle',
                'modal_accept' => 'Tümünü Kabul Et',
                'modal_reject' => 'Tümünü Reddet',
                'modal_save' => 'Kaydet'
            ],

            // Hindi
            'hi' => [
                'banner_text' => 'हम अपनी वेबसाइट पर आपके अनुभव को बेहतर बनाने के लिए कुकीज़ और समान तकनीकों का उपयोग करते हैं।',
                'banner_link' => 'हमारी <a href="%s">गोपनीयता नीति</a> पढ़ें।',
                'button_accept' => 'स्वीकार करें',
                'button_reject' => 'अस्वीकार करें',
                'button_settings' => 'सेटिंग्स प्रबंधित करें',
                'modal_title' => 'गोपनीयता सेटिंग्स',
                'modal_content' => 'यह वेबसाइट कुकीज़ और समान तकनीकों का उपयोग करती है। इन्हें श्रेणियों में बांटा गया है, जिन्हें आप नीचे देख और प्रबंधित कर सकते हैं। यदि आपने कोई गैर-आवश्यक कुकीज़ स्वीकार की हैं, तो आप सेटिंग्स में किसी भी समय अपनी प्राथमिकताएं बदल सकते हैं।',
                'modal_content_link' => 'हमारी <a href="%s">गोपनीयता नीति</a> में और जानें।',
                'functional_title' => 'कार्यात्मक',
                'preferences_title' => 'प्राथमिकताएं',
                'statistics_title' => 'सांख्यिकी',
                'marketing_title' => 'मार्केटिंग',
                'functional_content' => 'ये तकनीकें हमारी वेबसाइट की मूल कार्यक्षमता को सक्रिय करने के लिए आवश्यक हैं।',
                'preferences_content' => 'ये तकनीकें हमारी वेबसाइट को आपकी प्राथमिकताओं को याद रखने और आपको अधिक व्यक्तिगत अनुभव प्रदान करने की अनुमति देती हैं।',
                'statistics_content' => 'ये तकनीकें हमें प्रदर्शन को मापने और सुधारने के लिए हमारी वेबसाइट के उपयोग का विश्लेषण करने में सक्षम बनाती हैं।',
                'marketing_content' => 'ये तकनीकें हमारे मार्केटिंग भागीदारों द्वारा आपकी रुचियों के अनुरूप व्यक्तिगत विज्ञापन दिखाने के लिए उपयोग की जाती हैं।',
                'show_services' => 'सेवाएं दिखाएं',
                'hide_services' => 'सेवाएं छिपाएं',
                'modal_accept' => 'सभी स्वीकार करें',
                'modal_reject' => 'सभी अस्वीकार करें',
                'modal_save' => 'सहेजें'
            ],

            // Korean
            'ko' => [
                'banner_text' => '당사는 웹사이트에서 귀하의 경험을 개선하기 위해 쿠키 및 유사한 기술을 사용합니다.',
                'banner_link' => '<a href="%s">개인정보 보호정책</a>을 읽어보세요.',
                'button_accept' => '수락',
                'button_reject' => '거부',
                'button_settings' => '설정 관리',
                'modal_title' => '개인정보 설정',
                'modal_content' => '이 웹사이트는 쿠키 및 유사한 기술을 사용합니다. 이들은 아래에서 검토하고 관리할 수 있는 카테고리로 그룹화되어 있습니다. 필수가 아닌 쿠키를 수락한 경우 설정에서 언제든지 기본 설정을 변경할 수 있습니다.',
                'modal_content_link' => '<a href="%s">개인정보 보호정책</a>에서 자세히 알아보세요.',
                'functional_title' => '기능',
                'preferences_title' => '기본 설정',
                'statistics_title' => '통계',
                'marketing_title' => '마케팅',
                'functional_content' => '이러한 기술은 웹사이트의 핵심 기능을 활성화하는 데 필요합니다.',
                'preferences_content' => '이러한 기술을 통해 웹사이트가 귀하의 기본 설정을 기억하고 보다 개인화된 경험을 제공할 수 있습니다.',
                'statistics_content' => '이러한 기술을 통해 성능을 측정하고 개선하기 위해 웹사이트 사용을 분석할 수 있습니다.',
                'marketing_content' => '이러한 기술은 마케팅 파트너가 귀하의 관심사에 맞는 맞춤형 광고를 표시하는 데 사용됩니다.',
                'show_services' => '서비스 표시',
                'hide_services' => '서비스 숨기기',
                'modal_accept' => '모두 수락',
                'modal_reject' => '모두 거부',
                'modal_save' => '저장'
            ],

            // Arabic
            'ar' => [
                'banner_text' => 'نستخدم ملفات تعريف الارتباط والتقنيات المماثلة لتحسين تجربتك على موقعنا الإلكتروني.',
                'banner_link' => 'اقرأ <a href="%s">سياسة الخصوصية</a> الخاصة بنا.',
                'button_accept' => 'قبول',
                'button_reject' => 'رفض',
                'button_settings' => 'إدارة الإعدادات',
                'modal_title' => 'إعدادات الخصوصية',
                'modal_content' => 'يستخدم هذا الموقع ملفات تعريف الارتباط والتقنيات المماثلة. تم تجميعها في فئات يمكنك مراجعتها وإدارتها أدناه. إذا قبلت أي ملفات تعريف ارتباط غير ضرورية، يمكنك تغيير تفضيلاتك في أي وقت من الإعدادات.',
                'modal_content_link' => 'تعرف على المزيد في <a href="%s">سياسة الخصوصية</a> الخاصة بنا.',
                'functional_title' => 'وظيفية',
                'preferences_title' => 'التفضيلات',
                'statistics_title' => 'الإحصائيات',
                'marketing_title' => 'التسويق',
                'functional_content' => 'هذه التقنيات مطلوبة لتفعيل الوظائف الأساسية لموقعنا الإلكتروني.',
                'preferences_content' => 'تسمح هذه التقنيات لموقعنا الإلكتروني بتذكر تفضيلاتك وتزويدك بتجربة أكثر تخصيصاً.',
                'statistics_content' => 'تمكننا هذه التقنيات من تحليل استخدام موقعنا الإلكتروني لقياس الأداء وتحسينه.',
                'marketing_content' => 'تُستخدم هذه التقنيات من قبل شركائنا في التسويق لعرض إعلانات مخصصة ذات صلة باهتماماتك.',
                'show_services' => 'إظهار الخدمات',
                'hide_services' => 'إخفاء الخدمات',
                'modal_accept' => 'قبول الكل',
                'modal_reject' => 'رفض الكل',
                'modal_save' => 'حفظ'
            ],

            // German
            'de' => [
                'banner_text' => 'Wir verwenden Cookies und ähnliche Technologien, um Ihre Erfahrung auf unserer Website zu verbessern.',
                'banner_link' => 'Lesen Sie unsere <a href="%s">Datenschutzerklärung</a>.',
                'button_accept' => 'Akzeptieren',
                'button_reject' => 'Ablehnen',
                'button_settings' => 'Einstellungen verwalten',
                'modal_title' => 'Datenschutzeinstellungen',
                'modal_content' => 'Diese Website verwendet Cookies und ähnliche Technologien. Sie sind in Kategorien unterteilt, die Sie unten einsehen und verwalten können. Wenn Sie nicht-essentielle Cookies akzeptiert haben, können Sie Ihre Präferenzen jederzeit in den Einstellungen ändern.',
                'modal_content_link' => 'Erfahren Sie mehr in unserer <a href="%s">Datenschutzerklärung</a>.',
                'functional_title' => 'Funktional',
                'preferences_title' => 'Präferenzen',
                'statistics_title' => 'Statistiken',
                'marketing_title' => 'Marketing',
                'functional_content' => 'Diese Technologien sind erforderlich, um die Kernfunktionalität unserer Website zu aktivieren.',
                'preferences_content' => 'Diese Technologien ermöglichen es unserer Website, Ihre Präferenzen zu speichern und Ihnen ein personalisierteres Erlebnis zu bieten.',
                'statistics_content' => 'Diese Technologien ermöglichen es uns, die Nutzung unserer Website zu analysieren, um die Leistung zu messen und zu verbessern.',
                'marketing_content' => 'Diese Technologien werden von unseren Marketingpartnern verwendet, um Ihnen personalisierte Werbung zu zeigen, die für Ihre Interessen relevant ist.',
                'show_services' => 'Dienste anzeigen',
                'hide_services' => 'Dienste ausblenden',
                'modal_accept' => 'Alle akzeptieren',
                'modal_reject' => 'Alle ablehnen',
                'modal_save' => 'Speichern'
            ],

            // Chinese Simplified
            'zh' => [
                'banner_text' => '我们使用cookies和类似技术来改善您在我们网站上的体验。',
                'banner_link' => '阅读我们的<a href="%s">隐私政策</a>。',
                'button_accept' => '接受',
                'button_reject' => '拒绝',
                'button_settings' => '管理设置',
                'modal_title' => '隐私设置',
                'modal_content' => '本网站使用cookies和类似技术。它们被分为不同的类别，您可以在下面查看和管理。如果您接受了任何非必要的cookies，您可以随时在设置中更改您的偏好。',
                'modal_content_link' => '在我们的<a href="%s">隐私政策</a>中了解更多。',
                'functional_title' => '功能性',
                'preferences_title' => '偏好设置',
                'statistics_title' => '统计',
                'marketing_title' => '营销',
                'functional_content' => '这些技术是激活我们网站核心功能所必需的。',
                'preferences_content' => '这些技术使我们的网站能够记住您的偏好，并为您提供更个性化的体验。',
                'statistics_content' => '这些技术使我们能够分析网站的使用情况，以衡量和改进性能。',
                'marketing_content' => '这些技术被我们的营销合作伙伴用于向您展示与您兴趣相关的个性化广告。',
                'show_services' => '显示服务',
                'hide_services' => '隐藏服务',
                'modal_accept' => '全部接受',
                'modal_reject' => '全部拒绝',
                'modal_save' => '保存'
            ],

            // Spanish
            'es' => [
                'banner_text' => 'Utilizamos cookies y tecnologías similares para mejorar su experiencia en nuestro sitio web.',
                'banner_link' => 'Lea nuestra <a href="%s">Política de Privacidad</a>.',
                'button_accept' => 'Aceptar',
                'button_reject' => 'Rechazar',
                'button_settings' => 'Gestionar configuración',
                'modal_title' => 'Configuración de privacidad',
                'modal_content' => 'Este sitio web utiliza cookies y tecnologías similares. Están agrupadas en categorías que puede revisar y gestionar a continuación. Si ha aceptado cookies no esenciales, puede cambiar sus preferencias en cualquier momento en la configuración.',
                'modal_content_link' => 'Más información en nuestra <a href="%s">Política de Privacidad</a>.',
                'functional_title' => 'Funcional',
                'preferences_title' => 'Preferencias',
                'statistics_title' => 'Estadísticas',
                'marketing_title' => 'Marketing',
                'functional_content' => 'Estas tecnologías son necesarias para activar la funcionalidad principal de nuestro sitio web.',
                'preferences_content' => 'Estas tecnologías permiten que nuestro sitio web recuerde sus preferencias y le proporcione una experiencia más personalizada.',
                'statistics_content' => 'Estas tecnologías nos permiten analizar el uso de nuestro sitio web para medir y mejorar el rendimiento.',
                'marketing_content' => 'Estas tecnologías son utilizadas por nuestros socios de marketing para mostrarle anuncios personalizados relevantes a sus intereses.',
                'show_services' => 'Mostrar servicios',
                'hide_services' => 'Ocultar servicios',
                'modal_accept' => 'Aceptar todo',
                'modal_reject' => 'Rechazar todo',
                'modal_save' => 'Guardar'
            ],

            // French
            'fr' => [
                'banner_text' => 'Nous utilisons des cookies et des technologies similaires pour améliorer votre expérience sur notre site web.',
                'banner_link' => 'Lisez notre <a href="%s">Politique de confidentialité</a>.',
                'button_accept' => 'Accepter',
                'button_reject' => 'Refuser',
                'button_settings' => 'Gérer les paramètres',
                'modal_title' => 'Paramètres de confidentialité',
                'modal_content' => 'Ce site web utilise des cookies et des technologies similaires. Ils sont regroupés en catégories que vous pouvez consulter et gérer ci-dessous. Si vous avez accepté des cookies non essentiels, vous pouvez modifier vos préférences à tout moment dans les paramètres.',
                'modal_content_link' => 'En savoir plus dans notre <a href="%s">Politique de confidentialité</a>.',
                'functional_title' => 'Fonctionnel',
                'preferences_title' => 'Préférences',
                'statistics_title' => 'Statistiques',
                'marketing_title' => 'Marketing',
                'functional_content' => 'Ces technologies sont nécessaires pour activer les fonctionnalités de base de notre site web.',
                'preferences_content' => 'Ces technologies permettent à notre site web de mémoriser vos préférences et de vous offrir une expérience plus personnalisée.',
                'statistics_content' => 'Ces technologies nous permettent d\'analyser l\'utilisation de notre site web afin de mesurer et d\'améliorer les performances.',
                'marketing_content' => 'Ces technologies sont utilisées par nos partenaires marketing pour vous montrer des publicités personnalisées en rapport avec vos intérêts.',
                'show_services' => 'Afficher les services',
                'hide_services' => 'Masquer les services',
                'modal_accept' => 'Tout accepter',
                'modal_reject' => 'Tout refuser',
                'modal_save' => 'Enregistrer'
            ],

            // Portuguese
            'pt' => [
                'banner_text' => 'Utilizamos cookies e tecnologias semelhantes para melhorar a sua experiência no nosso site.',
                'banner_link' => 'Leia a nossa <a href="%s">Política de Privacidade</a>.',
                'button_accept' => 'Aceitar',
                'button_reject' => 'Rejeitar',
                'button_settings' => 'Gerir definições',
                'modal_title' => 'Definições de privacidade',
                'modal_content' => 'Este site utiliza cookies e tecnologias semelhantes. Estão agrupados em categorias que pode rever e gerir abaixo. Se aceitou cookies não essenciais, pode alterar as suas preferências a qualquer momento nas definições.',
                'modal_content_link' => 'Saiba mais na nossa <a href="%s">Política de Privacidade</a>.',
                'functional_title' => 'Funcional',
                'preferences_title' => 'Preferências',
                'statistics_title' => 'Estatísticas',
                'marketing_title' => 'Marketing',
                'functional_content' => 'Estas tecnologias são necessárias para ativar a funcionalidade principal do nosso site.',
                'preferences_content' => 'Estas tecnologias permitem que o nosso site lembre as suas preferências e lhe proporcione uma experiência mais personalizada.',
                'statistics_content' => 'Estas tecnologias permitem-nos analisar a utilização do nosso site para medir e melhorar o desempenho.',
                'marketing_content' => 'Estas tecnologias são utilizadas pelos nossos parceiros de marketing para lhe mostrar anúncios personalizados relevantes aos seus interesses.',
                'show_services' => 'Mostrar serviços',
                'hide_services' => 'Ocultar serviços',
                'modal_accept' => 'Aceitar tudo',
                'modal_reject' => 'Rejeitar tudo',
                'modal_save' => 'Guardar'
            ],

            // Russian
            'ru' => [
                'banner_text' => 'Мы используем файлы cookie и аналогичные технологии для улучшения вашего опыта на нашем сайте.',
                'banner_link' => 'Ознакомьтесь с нашей <a href="%s">Политикой конфиденциальности</a>.',
                'button_accept' => 'Принять',
                'button_reject' => 'Отклонить',
                'button_settings' => 'Управление настройками',
                'modal_title' => 'Настройки конфиденциальности',
                'modal_content' => 'Этот сайт использует файлы cookie и аналогичные технологии. Они сгруппированы по категориям, которые вы можете просмотреть и управлять ниже. Если вы приняли какие-либо необязательные файлы cookie, вы можете изменить свои предпочтения в любое время в настройках.',
                'modal_content_link' => 'Узнайте больше в нашей <a href="%s">Политике конфиденциальности</a>.',
                'functional_title' => 'Функциональные',
                'preferences_title' => 'Предпочтения',
                'statistics_title' => 'Статистика',
                'marketing_title' => 'Маркетинг',
                'functional_content' => 'Эти технологии необходимы для активации основных функций нашего сайта.',
                'preferences_content' => 'Эти технологии позволяют нашему сайту запоминать ваши предпочтения и предоставлять вам более персонализированный опыт.',
                'statistics_content' => 'Эти технологии позволяют нам анализировать использование нашего сайта для измерения и улучшения производительности.',
                'marketing_content' => 'Эти технологии используются нашими маркетинговыми партнерами для показа персонализированной рекламы, соответствующей вашим интересам.',
                'show_services' => 'Показать сервисы',
                'hide_services' => 'Скрыть сервисы',
                'modal_accept' => 'Принять все',
                'modal_reject' => 'Отклонить все',
                'modal_save' => 'Сохранить'
            ],

            // Japanese
            'ja' => [
                'banner_text' => '当サイトでは、お客様の体験を向上させるためにCookieおよび類似の技術を使用しています。',
                'banner_link' => '<a href="%s">プライバシーポリシー</a>をお読みください。',
                'button_accept' => '同意する',
                'button_reject' => '拒否する',
                'button_settings' => '設定を管理',
                'modal_title' => 'プライバシー設定',
                'modal_content' => '当ウェブサイトはCookieおよび類似の技術を使用しています。以下のカテゴリに分類されており、確認・管理することができます。必須でないCookieを受け入れた場合、設定でいつでも変更できます。',
                'modal_content_link' => '詳細は<a href="%s">プライバシーポリシー</a>をご覧ください。',
                'functional_title' => '機能性',
                'preferences_title' => '設定',
                'statistics_title' => '統計',
                'marketing_title' => 'マーケティング',
                'functional_content' => 'これらの技術は、当サイトの基本機能を有効にするために必要です。',
                'preferences_content' => 'これらの技術により、当サイトはお客様の設定を記憶し、より個人化された体験を提供できます。',
                'statistics_content' => 'これらの技術により、パフォーマンスを測定・改善するためにサイトの使用状況を分析できます。',
                'marketing_content' => 'これらの技術は、マーケティングパートナーがお客様の興味に関連するパーソナライズされた広告を表示するために使用されます。',
                'show_services' => 'サービスを表示',
                'hide_services' => 'サービスを非表示',
                'modal_accept' => 'すべて同意',
                'modal_reject' => 'すべて拒否',
                'modal_save' => '保存'
            ],

            // Indonesian
            'id' => [
                'banner_text' => 'Kami menggunakan cookie dan teknologi serupa untuk meningkatkan pengalaman Anda di situs web kami.',
                'banner_link' => 'Baca <a href="%s">Kebijakan Privasi</a> kami.',
                'button_accept' => 'Terima',
                'button_reject' => 'Tolak',
                'button_settings' => 'Kelola Pengaturan',
                'modal_title' => 'Pengaturan Privasi',
                'modal_content' => 'Situs web ini menggunakan cookie dan teknologi serupa. Mereka dikelompokkan ke dalam kategori yang dapat Anda tinjau dan kelola di bawah. Jika Anda telah menerima cookie yang tidak penting, Anda dapat mengubah preferensi Anda kapan saja di pengaturan.',
                'modal_content_link' => 'Pelajari lebih lanjut di <a href="%s">Kebijakan Privasi</a> kami.',
                'functional_title' => 'Fungsional',
                'preferences_title' => 'Preferensi',
                'statistics_title' => 'Statistik',
                'marketing_title' => 'Pemasaran',
                'functional_content' => 'Teknologi ini diperlukan untuk mengaktifkan fungsionalitas inti situs web kami.',
                'preferences_content' => 'Teknologi ini memungkinkan situs web kami mengingat preferensi Anda dan memberikan pengalaman yang lebih personal.',
                'statistics_content' => 'Teknologi ini memungkinkan kami menganalisis penggunaan situs web untuk mengukur dan meningkatkan kinerja.',
                'marketing_content' => 'Teknologi ini digunakan oleh mitra pemasaran kami untuk menampilkan iklan yang dipersonalisasi sesuai minat Anda.',
                'show_services' => 'Tampilkan Layanan',
                'hide_services' => 'Sembunyikan Layanan',
                'modal_accept' => 'Terima Semua',
                'modal_reject' => 'Tolak Semua',
                'modal_save' => 'Simpan'
            ],

            // Italian
            'it' => [
                'banner_text' => 'Utilizziamo cookie e tecnologie simili per migliorare la tua esperienza sul nostro sito web.',
                'banner_link' => 'Leggi la nostra <a href="%s">Informativa sulla Privacy</a>.',
                'button_accept' => 'Accetta',
                'button_reject' => 'Rifiuta',
                'button_settings' => 'Gestisci impostazioni',
                'modal_title' => 'Impostazioni sulla privacy',
                'modal_content' => 'Questo sito web utilizza cookie e tecnologie simili. Sono raggruppati in categorie che puoi rivedere e gestire qui sotto. Se hai accettato cookie non essenziali, puoi modificare le tue preferenze in qualsiasi momento nelle impostazioni.',
                'modal_content_link' => 'Scopri di più nella nostra <a href="%s">Informativa sulla Privacy</a>.',
                'functional_title' => 'Funzionali',
                'preferences_title' => 'Preferenze',
                'statistics_title' => 'Statistiche',
                'marketing_title' => 'Marketing',
                'functional_content' => 'Queste tecnologie sono necessarie per attivare le funzionalità principali del nostro sito web.',
                'preferences_content' => 'Queste tecnologie consentono al nostro sito web di ricordare le tue preferenze e offrirti un\'esperienza più personalizzata.',
                'statistics_content' => 'Queste tecnologie ci consentono di analizzare l\'utilizzo del nostro sito web per misurare e migliorare le prestazioni.',
                'marketing_content' => 'Queste tecnologie vengono utilizzate dai nostri partner di marketing per mostrarti pubblicità personalizzate pertinenti ai tuoi interessi.',
                'show_services' => 'Mostra servizi',
                'hide_services' => 'Nascondi servizi',
                'modal_accept' => 'Accetta tutto',
                'modal_reject' => 'Rifiuta tutto',
                'modal_save' => 'Salva'
            ],

            // Dutch
            'nl' => [
                'banner_text' => 'Wij gebruiken cookies en vergelijkbare technologieën om uw ervaring op onze website te verbeteren.',
                'banner_link' => 'Lees ons <a href="%s">Privacybeleid</a>.',
                'button_accept' => 'Accepteren',
                'button_reject' => 'Weigeren',
                'button_settings' => 'Instellingen beheren',
                'modal_title' => 'Privacy-instellingen',
                'modal_content' => 'Deze website maakt gebruik van cookies en vergelijkbare technologieën. Ze zijn gegroepeerd in categorieën die u hieronder kunt bekijken en beheren. Als u niet-essentiële cookies hebt geaccepteerd, kunt u uw voorkeuren op elk moment wijzigen in de instellingen.',
                'modal_content_link' => 'Meer informatie in ons <a href="%s">Privacybeleid</a>.',
                'functional_title' => 'Functioneel',
                'preferences_title' => 'Voorkeuren',
                'statistics_title' => 'Statistieken',
                'marketing_title' => 'Marketing',
                'functional_content' => 'Deze technologieën zijn nodig om de kernfunctionaliteit van onze website te activeren.',
                'preferences_content' => 'Deze technologieën stellen onze website in staat om uw voorkeuren te onthouden en u een meer gepersonaliseerde ervaring te bieden.',
                'statistics_content' => 'Deze technologieën stellen ons in staat om het gebruik van onze website te analyseren om de prestaties te meten en te verbeteren.',
                'marketing_content' => 'Deze technologieën worden door onze marketingpartners gebruikt om u gepersonaliseerde advertenties te tonen die relevant zijn voor uw interesses.',
                'show_services' => 'Diensten tonen',
                'hide_services' => 'Diensten verbergen',
                'modal_accept' => 'Alles accepteren',
                'modal_reject' => 'Alles weigeren',
                'modal_save' => 'Opslaan'
            ],

            // Polish
            'pl' => [
                'banner_text' => 'Używamy plików cookie i podobnych technologii, aby poprawić Twoje doświadczenia na naszej stronie.',
                'banner_link' => 'Przeczytaj naszą <a href="%s">Politykę Prywatności</a>.',
                'button_accept' => 'Akceptuj',
                'button_reject' => 'Odrzuć',
                'button_settings' => 'Zarządzaj ustawieniami',
                'modal_title' => 'Ustawienia prywatności',
                'modal_content' => 'Ta strona używa plików cookie i podobnych technologii. Są one pogrupowane w kategorie, które możesz przeglądać i zarządzać poniżej. Jeśli zaakceptowałeś nieistotne pliki cookie, możesz zmienić swoje preferencje w dowolnym momencie w ustawieniach.',
                'modal_content_link' => 'Dowiedz się więcej w naszej <a href="%s">Polityce Prywatności</a>.',
                'functional_title' => 'Funkcjonalne',
                'preferences_title' => 'Preferencje',
                'statistics_title' => 'Statystyki',
                'marketing_title' => 'Marketing',
                'functional_content' => 'Te technologie są wymagane do aktywacji podstawowych funkcji naszej strony.',
                'preferences_content' => 'Te technologie pozwalają naszej stronie zapamiętać Twoje preferencje i zapewnić bardziej spersonalizowane doświadczenie.',
                'statistics_content' => 'Te technologie umożliwiają nam analizę korzystania z naszej strony w celu mierzenia i poprawy wydajności.',
                'marketing_content' => 'Te technologie są używane przez naszych partnerów marketingowych do wyświetlania spersonalizowanych reklam odpowiednich do Twoich zainteresowań.',
                'show_services' => 'Pokaż usługi',
                'hide_services' => 'Ukryj usługi',
                'modal_accept' => 'Akceptuj wszystko',
                'modal_reject' => 'Odrzuć wszystko',
                'modal_save' => 'Zapisz'
            ],

            // Vietnamese
            'vi' => [
                'banner_text' => 'Chúng tôi sử dụng cookie và các công nghệ tương tự để cải thiện trải nghiệm của bạn trên trang web.',
                'banner_link' => 'Đọc <a href="%s">Chính sách Bảo mật</a> của chúng tôi.',
                'button_accept' => 'Chấp nhận',
                'button_reject' => 'Từ chối',
                'button_settings' => 'Quản lý cài đặt',
                'modal_title' => 'Cài đặt Bảo mật',
                'modal_content' => 'Trang web này sử dụng cookie và các công nghệ tương tự. Chúng được nhóm thành các danh mục mà bạn có thể xem xét và quản lý bên dưới. Nếu bạn đã chấp nhận bất kỳ cookie không thiết yếu nào, bạn có thể thay đổi tùy chọn của mình bất cứ lúc nào trong cài đặt.',
                'modal_content_link' => 'Tìm hiểu thêm trong <a href="%s">Chính sách Bảo mật</a> của chúng tôi.',
                'functional_title' => 'Chức năng',
                'preferences_title' => 'Tùy chọn',
                'statistics_title' => 'Thống kê',
                'marketing_title' => 'Tiếp thị',
                'functional_content' => 'Các công nghệ này cần thiết để kích hoạt chức năng cốt lõi của trang web.',
                'preferences_content' => 'Các công nghệ này cho phép trang web ghi nhớ tùy chọn của bạn và cung cấp trải nghiệm cá nhân hóa hơn.',
                'statistics_content' => 'Các công nghệ này cho phép chúng tôi phân tích việc sử dụng trang web để đo lường và cải thiện hiệu suất.',
                'marketing_content' => 'Các công nghệ này được sử dụng bởi các đối tác tiếp thị để hiển thị quảng cáo cá nhân hóa phù hợp với sở thích của bạn.',
                'show_services' => 'Hiển thị dịch vụ',
                'hide_services' => 'Ẩn dịch vụ',
                'modal_accept' => 'Chấp nhận tất cả',
                'modal_reject' => 'Từ chối tất cả',
                'modal_save' => 'Lưu'
            ],

            // Thai
            'th' => [
                'banner_text' => 'เราใช้คุกกี้และเทคโนโลยีที่คล้ายกันเพื่อปรับปรุงประสบการณ์ของคุณบนเว็บไซต์ของเรา',
                'banner_link' => 'อ่าน<a href="%s">นโยบายความเป็นส่วนตัว</a>ของเรา',
                'button_accept' => 'ยอมรับ',
                'button_reject' => 'ปฏิเสธ',
                'button_settings' => 'จัดการการตั้งค่า',
                'modal_title' => 'การตั้งค่าความเป็นส่วนตัว',
                'modal_content' => 'เว็บไซต์นี้ใช้คุกกี้และเทคโนโลยีที่คล้ายกัน พวกเขาถูกจัดกลุ่มเป็นหมวดหมู่ที่คุณสามารถตรวจสอบและจัดการด้านล่าง หากคุณยอมรับคุกกี้ที่ไม่จำเป็นใดๆ คุณสามารถเปลี่ยนการตั้งค่าได้ตลอดเวลาในการตั้งค่า',
                'modal_content_link' => 'เรียนรู้เพิ่มเติมใน<a href="%s">นโยบายความเป็นส่วนตัว</a>ของเรา',
                'functional_title' => 'ฟังก์ชัน',
                'preferences_title' => 'การตั้งค่า',
                'statistics_title' => 'สถิติ',
                'marketing_title' => 'การตลาด',
                'functional_content' => 'เทคโนโลยีเหล่านี้จำเป็นสำหรับการเปิดใช้งานฟังก์ชันหลักของเว็บไซต์',
                'preferences_content' => 'เทคโนโลยีเหล่านี้ช่วยให้เว็บไซต์จดจำการตั้งค่าของคุณและมอบประสบการณ์ที่เป็นส่วนตัวมากขึ้น',
                'statistics_content' => 'เทคโนโลยีเหล่านี้ช่วยให้เราวิเคราะห์การใช้งานเว็บไซต์เพื่อวัดและปรับปรุงประสิทธิภาพ',
                'marketing_content' => 'เทคโนโลยีเหล่านี้ถูกใช้โดยพันธมิตรทางการตลาดเพื่อแสดงโฆษณาที่เหมาะกับความสนใจของคุณ',
                'show_services' => 'แสดงบริการ',
                'hide_services' => 'ซ่อนบริการ',
                'modal_accept' => 'ยอมรับทั้งหมด',
                'modal_reject' => 'ปฏิเสธทั้งหมด',
                'modal_save' => 'บันทึก'
            ],

            // Ukrainian
            'uk' => [
                'banner_text' => 'Ми використовуємо файли cookie та подібні технології для покращення вашого досвіду на нашому сайті.',
                'banner_link' => 'Ознайомтеся з нашою <a href="%s">Політикою конфіденційності</a>.',
                'button_accept' => 'Прийняти',
                'button_reject' => 'Відхилити',
                'button_settings' => 'Керувати налаштуваннями',
                'modal_title' => 'Налаштування конфіденційності',
                'modal_content' => 'Цей сайт використовує файли cookie та подібні технології. Вони згруповані за категоріями, які ви можете переглянути та керувати нижче. Якщо ви прийняли будь-які необов\'язкові файли cookie, ви можете змінити свої налаштування в будь-який час.',
                'modal_content_link' => 'Дізнайтеся більше в нашій <a href="%s">Політиці конфіденційності</a>.',
                'functional_title' => 'Функціональні',
                'preferences_title' => 'Налаштування',
                'statistics_title' => 'Статистика',
                'marketing_title' => 'Маркетинг',
                'functional_content' => 'Ці технології необхідні для активації основних функцій нашого сайту.',
                'preferences_content' => 'Ці технології дозволяють нашому сайту запам\'ятовувати ваші налаштування та надавати більш персоналізований досвід.',
                'statistics_content' => 'Ці технології дозволяють нам аналізувати використання нашого сайту для вимірювання та покращення продуктивності.',
                'marketing_content' => 'Ці технології використовуються нашими маркетинговими партнерами для показу персоналізованої реклами, що відповідає вашим інтересам.',
                'show_services' => 'Показати сервіси',
                'hide_services' => 'Приховати сервіси',
                'modal_accept' => 'Прийняти все',
                'modal_reject' => 'Відхилити все',
                'modal_save' => 'Зберегти'
            ],

            // Czech
            'cs' => [
                'banner_text' => 'Používáme soubory cookie a podobné technologie ke zlepšení vašeho zážitku na našich stránkách.',
                'banner_link' => 'Přečtěte si naše <a href="%s">Zásady ochrany osobních údajů</a>.',
                'button_accept' => 'Přijmout',
                'button_reject' => 'Odmítnout',
                'button_settings' => 'Spravovat nastavení',
                'modal_title' => 'Nastavení soukromí',
                'modal_content' => 'Tyto webové stránky používají soubory cookie a podobné technologie. Jsou seskupeny do kategorií, které si můžete prohlédnout a spravovat níže. Pokud jste přijali jakékoli nepodstatné soubory cookie, můžete své preference kdykoli změnit v nastavení.',
                'modal_content_link' => 'Další informace najdete v našich <a href="%s">Zásadách ochrany osobních údajů</a>.',
                'functional_title' => 'Funkční',
                'preferences_title' => 'Předvolby',
                'statistics_title' => 'Statistiky',
                'marketing_title' => 'Marketing',
                'functional_content' => 'Tyto technologie jsou nutné k aktivaci základních funkcí našich webových stránek.',
                'preferences_content' => 'Tyto technologie umožňují našim stránkám zapamatovat si vaše preference a poskytnout vám osobnější zážitek.',
                'statistics_content' => 'Tyto technologie nám umožňují analyzovat používání našich stránek za účelem měření a zlepšování výkonu.',
                'marketing_content' => 'Tyto technologie používají naši marketingoví partneři k zobrazování personalizovaných reklam relevantních pro vaše zájmy.',
                'show_services' => 'Zobrazit služby',
                'hide_services' => 'Skrýt služby',
                'modal_accept' => 'Přijmout vše',
                'modal_reject' => 'Odmítnout vše',
                'modal_save' => 'Uložit'
            ],

            // Greek
            'el' => [
                'banner_text' => 'Χρησιμοποιούμε cookies και παρόμοιες τεχνολογίες για να βελτιώσουμε την εμπειρία σας στον ιστότοπό μας.',
                'banner_link' => 'Διαβάστε την <a href="%s">Πολιτική Απορρήτου</a> μας.',
                'button_accept' => 'Αποδοχή',
                'button_reject' => 'Απόρριψη',
                'button_settings' => 'Διαχείριση ρυθμίσεων',
                'modal_title' => 'Ρυθμίσεις Απορρήτου',
                'modal_content' => 'Αυτός ο ιστότοπος χρησιμοποιεί cookies και παρόμοιες τεχνολογίες. Είναι ομαδοποιημένα σε κατηγορίες που μπορείτε να δείτε και να διαχειριστείτε παρακάτω. Αν έχετε αποδεχτεί μη απαραίτητα cookies, μπορείτε να αλλάξετε τις προτιμήσεις σας ανά πάσα στιγμή στις ρυθμίσεις.',
                'modal_content_link' => 'Μάθετε περισσότερα στην <a href="%s">Πολιτική Απορρήτου</a> μας.',
                'functional_title' => 'Λειτουργικά',
                'preferences_title' => 'Προτιμήσεις',
                'statistics_title' => 'Στατιστικά',
                'marketing_title' => 'Μάρκετινγκ',
                'functional_content' => 'Αυτές οι τεχνολογίες απαιτούνται για την ενεργοποίηση των βασικών λειτουργιών του ιστότοπού μας.',
                'preferences_content' => 'Αυτές οι τεχνολογίες επιτρέπουν στον ιστότοπό μας να θυμάται τις προτιμήσεις σας και να σας παρέχει πιο εξατομικευμένη εμπειρία.',
                'statistics_content' => 'Αυτές οι τεχνολογίες μας επιτρέπουν να αναλύουμε τη χρήση του ιστότοπού μας για να μετράμε και να βελτιώνουμε την απόδοση.',
                'marketing_content' => 'Αυτές οι τεχνολογίες χρησιμοποιούνται από τους συνεργάτες μάρκετινγκ μας για να σας εμφανίζουν εξατομικευμένες διαφημίσεις σχετικές με τα ενδιαφέροντά σας.',
                'show_services' => 'Εμφάνιση υπηρεσιών',
                'hide_services' => 'Απόκρυψη υπηρεσιών',
                'modal_accept' => 'Αποδοχή όλων',
                'modal_reject' => 'Απόρριψη όλων',
                'modal_save' => 'Αποθήκευση'
            ],

            // Romanian
            'ro' => [
                'banner_text' => 'Folosim cookie-uri și tehnologii similare pentru a îmbunătăți experiența dumneavoastră pe site-ul nostru.',
                'banner_link' => 'Citiți <a href="%s">Politica de Confidențialitate</a>.',
                'button_accept' => 'Accept',
                'button_reject' => 'Refuz',
                'button_settings' => 'Gestionare setări',
                'modal_title' => 'Setări de confidențialitate',
                'modal_content' => 'Acest site folosește cookie-uri și tehnologii similare. Sunt grupate în categorii pe care le puteți examina și gestiona mai jos. Dacă ați acceptat cookie-uri neesențiale, puteți modifica preferințele oricând din setări.',
                'modal_content_link' => 'Aflați mai multe în <a href="%s">Politica de Confidențialitate</a>.',
                'functional_title' => 'Funcționale',
                'preferences_title' => 'Preferințe',
                'statistics_title' => 'Statistici',
                'marketing_title' => 'Marketing',
                'functional_content' => 'Aceste tehnologii sunt necesare pentru activarea funcționalității de bază a site-ului nostru.',
                'preferences_content' => 'Aceste tehnologii permit site-ului nostru să rețină preferințele și să vă ofere o experiență mai personalizată.',
                'statistics_content' => 'Aceste tehnologii ne permit să analizăm utilizarea site-ului pentru a măsura și îmbunătăți performanța.',
                'marketing_content' => 'Aceste tehnologii sunt utilizate de partenerii noștri de marketing pentru a vă afișa reclame personalizate relevante pentru interesele dumneavoastră.',
                'show_services' => 'Afișare servicii',
                'hide_services' => 'Ascundere servicii',
                'modal_accept' => 'Accept toate',
                'modal_reject' => 'Refuz toate',
                'modal_save' => 'Salvare'
            ],

            // Hungarian
            'hu' => [
                'banner_text' => 'Cookie-kat és hasonló technológiákat használunk a weboldalunkon szerzett élmény javítása érdekében.',
                'banner_link' => 'Olvassa el <a href="%s">Adatvédelmi irányelveinket</a>.',
                'button_accept' => 'Elfogadom',
                'button_reject' => 'Elutasítom',
                'button_settings' => 'Beállítások kezelése',
                'modal_title' => 'Adatvédelmi beállítások',
                'modal_content' => 'Ez a weboldal cookie-kat és hasonló technológiákat használ. Kategóriákba vannak csoportosítva, amelyeket alább áttekinthet és kezelhet. Ha elfogadott nem lényeges cookie-kat, bármikor módosíthatja beállításait.',
                'modal_content_link' => 'További információ az <a href="%s">Adatvédelmi irányelveinkben</a>.',
                'functional_title' => 'Funkcionális',
                'preferences_title' => 'Beállítások',
                'statistics_title' => 'Statisztikák',
                'marketing_title' => 'Marketing',
                'functional_content' => 'Ezek a technológiák szükségesek weboldalunk alapvető funkcióinak aktiválásához.',
                'preferences_content' => 'Ezek a technológiák lehetővé teszik weboldalunknak, hogy megjegyezze beállításait és személyre szabottabb élményt nyújtson.',
                'statistics_content' => 'Ezek a technológiák lehetővé teszik számunkra a weboldal használatának elemzését a teljesítmény mérése és javítása érdekében.',
                'marketing_content' => 'Ezeket a technológiákat marketing partnereink használják az érdeklődési körének megfelelő személyre szabott hirdetések megjelenítésére.',
                'show_services' => 'Szolgáltatások megjelenítése',
                'hide_services' => 'Szolgáltatások elrejtése',
                'modal_accept' => 'Összes elfogadása',
                'modal_reject' => 'Összes elutasítása',
                'modal_save' => 'Mentés'
            ],

            // Swedish
            'sv' => [
                'banner_text' => 'Vi använder cookies och liknande teknik för att förbättra din upplevelse på vår webbplats.',
                'banner_link' => 'Läs vår <a href="%s">Integritetspolicy</a>.',
                'button_accept' => 'Acceptera',
                'button_reject' => 'Avvisa',
                'button_settings' => 'Hantera inställningar',
                'modal_title' => 'Sekretessinställningar',
                'modal_content' => 'Denna webbplats använder cookies och liknande teknik. De är grupperade i kategorier som du kan granska och hantera nedan. Om du har accepterat icke-nödvändiga cookies kan du ändra dina inställningar när som helst.',
                'modal_content_link' => 'Läs mer i vår <a href="%s">Integritetspolicy</a>.',
                'functional_title' => 'Funktionella',
                'preferences_title' => 'Inställningar',
                'statistics_title' => 'Statistik',
                'marketing_title' => 'Marknadsföring',
                'functional_content' => 'Dessa tekniker krävs för att aktivera kärnfunktionerna på vår webbplats.',
                'preferences_content' => 'Dessa tekniker gör det möjligt för vår webbplats att komma ihåg dina inställningar och ge dig en mer personlig upplevelse.',
                'statistics_content' => 'Dessa tekniker gör det möjligt för oss att analysera användningen av vår webbplats för att mäta och förbättra prestanda.',
                'marketing_content' => 'Dessa tekniker används av våra marknadsföringspartners för att visa dig personliga annonser som är relevanta för dina intressen.',
                'show_services' => 'Visa tjänster',
                'hide_services' => 'Dölj tjänster',
                'modal_accept' => 'Acceptera alla',
                'modal_reject' => 'Avvisa alla',
                'modal_save' => 'Spara'
            ],

            // Danish
            'da' => [
                'banner_text' => 'Vi bruger cookies og lignende teknologier for at forbedre din oplevelse på vores hjemmeside.',
                'banner_link' => 'Læs vores <a href="%s">Privatlivspolitik</a>.',
                'button_accept' => 'Accepter',
                'button_reject' => 'Afvis',
                'button_settings' => 'Administrer indstillinger',
                'modal_title' => 'Privatlivsindstillinger',
                'modal_content' => 'Denne hjemmeside bruger cookies og lignende teknologier. De er grupperet i kategorier, som du kan gennemse og administrere nedenfor. Hvis du har accepteret ikke-essentielle cookies, kan du ændre dine præferencer når som helst i indstillingerne.',
                'modal_content_link' => 'Læs mere i vores <a href="%s">Privatlivspolitik</a>.',
                'functional_title' => 'Funktionelle',
                'preferences_title' => 'Præferencer',
                'statistics_title' => 'Statistik',
                'marketing_title' => 'Marketing',
                'functional_content' => 'Disse teknologier er nødvendige for at aktivere kernefunktionaliteten på vores hjemmeside.',
                'preferences_content' => 'Disse teknologier gør det muligt for vores hjemmeside at huske dine præferencer og give dig en mere personlig oplevelse.',
                'statistics_content' => 'Disse teknologier gør det muligt for os at analysere brugen af vores hjemmeside for at måle og forbedre ydeevnen.',
                'marketing_content' => 'Disse teknologier bruges af vores marketingpartnere til at vise dig personlige annoncer, der er relevante for dine interesser.',
                'show_services' => 'Vis tjenester',
                'hide_services' => 'Skjul tjenester',
                'modal_accept' => 'Accepter alle',
                'modal_reject' => 'Afvis alle',
                'modal_save' => 'Gem'
            ],

            // Finnish
            'fi' => [
                'banner_text' => 'Käytämme evästeitä ja vastaavia teknologioita parantaaksemme kokemustasi verkkosivustollamme.',
                'banner_link' => 'Lue <a href="%s">Tietosuojakäytäntömme</a>.',
                'button_accept' => 'Hyväksy',
                'button_reject' => 'Hylkää',
                'button_settings' => 'Hallitse asetuksia',
                'modal_title' => 'Yksityisyysasetukset',
                'modal_content' => 'Tämä verkkosivusto käyttää evästeitä ja vastaavia teknologioita. Ne on ryhmitelty kategorioihin, joita voit tarkastella ja hallita alla. Jos olet hyväksynyt ei-välttämättömiä evästeitä, voit muuttaa asetuksiasi milloin tahansa.',
                'modal_content_link' => 'Lue lisää <a href="%s">Tietosuojakäytännöstämme</a>.',
                'functional_title' => 'Toiminnalliset',
                'preferences_title' => 'Asetukset',
                'statistics_title' => 'Tilastot',
                'marketing_title' => 'Markkinointi',
                'functional_content' => 'Nämä teknologiat ovat välttämättömiä verkkosivustomme ydintoimintojen aktivoimiseksi.',
                'preferences_content' => 'Nämä teknologiat mahdollistavat verkkosivustomme muistamaan asetuksesi ja tarjoamaan sinulle henkilökohtaisemman kokemuksen.',
                'statistics_content' => 'Nämä teknologiat mahdollistavat verkkosivustomme käytön analysoinnin suorituskyvyn mittaamiseksi ja parantamiseksi.',
                'marketing_content' => 'Markkinointikumppanimme käyttävät näitä teknologioita näyttääkseen sinulle kiinnostuksiisi sopivia henkilökohtaisia mainoksia.',
                'show_services' => 'Näytä palvelut',
                'hide_services' => 'Piilota palvelut',
                'modal_accept' => 'Hyväksy kaikki',
                'modal_reject' => 'Hylkää kaikki',
                'modal_save' => 'Tallenna'
            ],

            // Norwegian
            'nb' => [
                'banner_text' => 'Vi bruker informasjonskapsler og lignende teknologier for å forbedre din opplevelse på nettstedet vårt.',
                'banner_link' => 'Les vår <a href="%s">Personvernerklæring</a>.',
                'button_accept' => 'Godta',
                'button_reject' => 'Avvis',
                'button_settings' => 'Administrer innstillinger',
                'modal_title' => 'Personverninnstillinger',
                'modal_content' => 'Dette nettstedet bruker informasjonskapsler og lignende teknologier. De er gruppert i kategorier som du kan gjennomgå og administrere nedenfor. Hvis du har godtatt ikke-essensielle informasjonskapsler, kan du endre preferansene dine når som helst i innstillingene.',
                'modal_content_link' => 'Les mer i vår <a href="%s">Personvernerklæring</a>.',
                'functional_title' => 'Funksjonelle',
                'preferences_title' => 'Preferanser',
                'statistics_title' => 'Statistikk',
                'marketing_title' => 'Markedsføring',
                'functional_content' => 'Disse teknologiene er nødvendige for å aktivere kjernefunksjonaliteten på nettstedet vårt.',
                'preferences_content' => 'Disse teknologiene lar nettstedet vårt huske preferansene dine og gi deg en mer personlig opplevelse.',
                'statistics_content' => 'Disse teknologiene gjør det mulig for oss å analysere bruken av nettstedet vårt for å måle og forbedre ytelsen.',
                'marketing_content' => 'Disse teknologiene brukes av markedsføringspartnerne våre for å vise deg personlige annonser som er relevante for dine interesser.',
                'show_services' => 'Vis tjenester',
                'hide_services' => 'Skjul tjenester',
                'modal_accept' => 'Godta alle',
                'modal_reject' => 'Avvis alle',
                'modal_save' => 'Lagre'
            ],

            // Hebrew
            'he' => [
                'banner_text' => 'אנו משתמשים בעוגיות וטכנולוגיות דומות כדי לשפר את החוויה שלך באתר שלנו.',
                'banner_link' => 'קרא את <a href="%s">מדיניות הפרטיות</a> שלנו.',
                'button_accept' => 'אשר',
                'button_reject' => 'דחה',
                'button_settings' => 'נהל הגדרות',
                'modal_title' => 'הגדרות פרטיות',
                'modal_content' => 'אתר זה משתמש בעוגיות וטכנולוגיות דומות. הן מקובצות לקטגוריות שתוכל לעיין בהן ולנהל למטה. אם קיבלת עוגיות לא חיוניות, תוכל לשנות את ההעדפות שלך בכל עת בהגדרות.',
                'modal_content_link' => 'למד עוד ב<a href="%s">מדיניות הפרטיות</a> שלנו.',
                'functional_title' => 'פונקציונלי',
                'preferences_title' => 'העדפות',
                'statistics_title' => 'סטטיסטיקה',
                'marketing_title' => 'שיווק',
                'functional_content' => 'טכנולוגיות אלה נדרשות להפעלת הפונקציונליות הבסיסית של האתר שלנו.',
                'preferences_content' => 'טכנולוגיות אלה מאפשרות לאתר שלנו לזכור את ההעדפות שלך ולספק לך חוויה מותאמת אישית יותר.',
                'statistics_content' => 'טכנולוגיות אלה מאפשרות לנו לנתח את השימוש באתר שלנו כדי למדוד ולשפר את הביצועים.',
                'marketing_content' => 'טכנולוגיות אלה משמשות את שותפי השיווק שלנו כדי להציג לך פרסומות מותאמות אישית הרלוונטיות לתחומי העניין שלך.',
                'show_services' => 'הצג שירותים',
                'hide_services' => 'הסתר שירותים',
                'modal_accept' => 'אשר הכל',
                'modal_reject' => 'דחה הכל',
                'modal_save' => 'שמור'
            ],

            // Malay
            'ms' => [
                'banner_text' => 'Kami menggunakan kuki dan teknologi serupa untuk meningkatkan pengalaman anda di laman web kami.',
                'banner_link' => 'Baca <a href="%s">Dasar Privasi</a> kami.',
                'button_accept' => 'Terima',
                'button_reject' => 'Tolak',
                'button_settings' => 'Urus Tetapan',
                'modal_title' => 'Tetapan Privasi',
                'modal_content' => 'Laman web ini menggunakan kuki dan teknologi serupa. Ia dikumpulkan dalam kategori yang boleh anda semak dan urus di bawah. Jika anda telah menerima kuki yang tidak penting, anda boleh menukar pilihan anda pada bila-bila masa dalam tetapan.',
                'modal_content_link' => 'Ketahui lebih lanjut dalam <a href="%s">Dasar Privasi</a> kami.',
                'functional_title' => 'Fungsional',
                'preferences_title' => 'Keutamaan',
                'statistics_title' => 'Statistik',
                'marketing_title' => 'Pemasaran',
                'functional_content' => 'Teknologi ini diperlukan untuk mengaktifkan fungsi teras laman web kami.',
                'preferences_content' => 'Teknologi ini membolehkan laman web kami mengingati pilihan anda dan memberikan pengalaman yang lebih peribadi.',
                'statistics_content' => 'Teknologi ini membolehkan kami menganalisis penggunaan laman web untuk mengukur dan meningkatkan prestasi.',
                'marketing_content' => 'Teknologi ini digunakan oleh rakan pemasaran kami untuk menunjukkan iklan yang diperibadikan berkaitan dengan minat anda.',
                'show_services' => 'Tunjukkan Perkhidmatan',
                'hide_services' => 'Sembunyikan Perkhidmatan',
                'modal_accept' => 'Terima Semua',
                'modal_reject' => 'Tolak Semua',
                'modal_save' => 'Simpan'
            ],

            // Bengali
            'bn' => [
                'banner_text' => 'আমরা আমাদের ওয়েবসাইটে আপনার অভিজ্ঞতা উন্নত করতে কুকি এবং অনুরূপ প্রযুক্তি ব্যবহার করি।',
                'banner_link' => 'আমাদের <a href="%s">গোপনীয়তা নীতি</a> পড়ুন।',
                'button_accept' => 'গ্রহণ করুন',
                'button_reject' => 'প্রত্যাখ্যান করুন',
                'button_settings' => 'সেটিংস পরিচালনা করুন',
                'modal_title' => 'গোপনীয়তা সেটিংস',
                'modal_content' => 'এই ওয়েবসাইটটি কুকি এবং অনুরূপ প্রযুক্তি ব্যবহার করে। এগুলি বিভাগগুলিতে গ্রুপ করা হয়েছে যা আপনি নীচে পর্যালোচনা এবং পরিচালনা করতে পারেন। আপনি যদি কোনো অপ্রয়োজনীয় কুকি গ্রহণ করে থাকেন, আপনি যেকোনো সময় সেটিংসে আপনার পছন্দ পরিবর্তন করতে পারেন।',
                'modal_content_link' => 'আমাদের <a href="%s">গোপনীয়তা নীতিতে</a> আরও জানুন।',
                'functional_title' => 'কার্যকরী',
                'preferences_title' => 'পছন্দসমূহ',
                'statistics_title' => 'পরিসংখ্যান',
                'marketing_title' => 'বিপণন',
                'functional_content' => 'এই প্রযুক্তিগুলি আমাদের ওয়েবসাইটের মূল কার্যকারিতা সক্রিয় করতে প্রয়োজন।',
                'preferences_content' => 'এই প্রযুক্তিগুলি আমাদের ওয়েবসাইটকে আপনার পছন্দ মনে রাখতে এবং আপনাকে আরও ব্যক্তিগতকৃত অভিজ্ঞতা প্রদান করতে দেয়।',
                'statistics_content' => 'এই প্রযুক্তিগুলি আমাদের কর্মক্ষমতা পরিমাপ এবং উন্নত করতে ওয়েবসাইট ব্যবহার বিশ্লেষণ করতে সক্ষম করে।',
                'marketing_content' => 'এই প্রযুক্তিগুলি আমাদের বিপণন অংশীদাররা আপনার আগ্রহের সাথে প্রাসঙ্গিক ব্যক্তিগতকৃত বিজ্ঞাপন দেখাতে ব্যবহার করে।',
                'show_services' => 'সেবা দেখান',
                'hide_services' => 'সেবা লুকান',
                'modal_accept' => 'সব গ্রহণ করুন',
                'modal_reject' => 'সব প্রত্যাখ্যান করুন',
                'modal_save' => 'সংরক্ষণ করুন'
            ],

            // Persian/Farsi
            'fa' => [
                'banner_text' => 'ما از کوکی‌ها و فناوری‌های مشابه برای بهبود تجربه شما در وب‌سایت خود استفاده می‌کنیم.',
                'banner_link' => '<a href="%s">سیاست حفظ حریم خصوصی</a> ما را بخوانید.',
                'button_accept' => 'پذیرش',
                'button_reject' => 'رد کردن',
                'button_settings' => 'مدیریت تنظیمات',
                'modal_title' => 'تنظیمات حریم خصوصی',
                'modal_content' => 'این وب‌سایت از کوکی‌ها و فناوری‌های مشابه استفاده می‌کند. آنها در دسته‌بندی‌هایی گروه‌بندی شده‌اند که می‌توانید در زیر بررسی و مدیریت کنید. اگر کوکی‌های غیرضروری را پذیرفته‌اید، می‌توانید ترجیحات خود را هر زمان در تنظیمات تغییر دهید.',
                'modal_content_link' => 'در <a href="%s">سیاست حفظ حریم خصوصی</a> ما بیشتر بیاموزید.',
                'functional_title' => 'عملکردی',
                'preferences_title' => 'ترجیحات',
                'statistics_title' => 'آمار',
                'marketing_title' => 'بازاریابی',
                'functional_content' => 'این فناوری‌ها برای فعال کردن عملکرد اصلی وب‌سایت ما ضروری هستند.',
                'preferences_content' => 'این فناوری‌ها به وب‌سایت ما اجازه می‌دهند ترجیحات شما را به خاطر بسپارد و تجربه شخصی‌سازی شده‌تری ارائه دهد.',
                'statistics_content' => 'این فناوری‌ها به ما امکان تحلیل استفاده از وب‌سایت را برای اندازه‌گیری و بهبود عملکرد می‌دهند.',
                'marketing_content' => 'این فناوری‌ها توسط شرکای بازاریابی ما برای نمایش تبلیغات شخصی‌سازی شده مرتبط با علایق شما استفاده می‌شوند.',
                'show_services' => 'نمایش خدمات',
                'hide_services' => 'پنهان کردن خدمات',
                'modal_accept' => 'پذیرش همه',
                'modal_reject' => 'رد همه',
                'modal_save' => 'ذخیره'
            ],

            // Tamil
            'ta' => [
                'banner_text' => 'எங்கள் இணையதளத்தில் உங்கள் அனுபவத்தை மேம்படுத்த குக்கீகள் மற்றும் ஒத்த தொழில்நுட்பங்களைப் பயன்படுத்துகிறோம்.',
                'banner_link' => 'எங்கள் <a href="%s">தனியுரிமைக் கொள்கையைப்</a> படிக்கவும்.',
                'button_accept' => 'ஏற்றுக்கொள்',
                'button_reject' => 'நிராகரி',
                'button_settings' => 'அமைப்புகளை நிர்வகி',
                'modal_title' => 'தனியுரிமை அமைப்புகள்',
                'modal_content' => 'இந்த இணையதளம் குக்கீகள் மற்றும் ஒத்த தொழில்நுட்பங்களைப் பயன்படுத்துகிறது. அவை கீழே நீங்கள் மதிப்பாய்வு செய்து நிர்வகிக்கக்கூடிய வகைகளாக தொகுக்கப்பட்டுள்ளன. அத்தியாவசியமற்ற குக்கீகளை ஏற்றுக்கொண்டிருந்தால், அமைப்புகளில் எந்த நேரத்திலும் உங்கள் விருப்பங்களை மாற்றலாம்.',
                'modal_content_link' => 'எங்கள் <a href="%s">தனியுரிமைக் கொள்கையில்</a> மேலும் அறியவும்.',
                'functional_title' => 'செயல்பாட்டு',
                'preferences_title' => 'விருப்பத்தேர்வுகள்',
                'statistics_title' => 'புள்ளிவிவரங்கள்',
                'marketing_title' => 'சந்தைப்படுத்தல்',
                'functional_content' => 'எங்கள் இணையதளத்தின் முக்கிய செயல்பாட்டை செயல்படுத்த இந்த தொழில்நுட்பங்கள் தேவை.',
                'preferences_content' => 'இந்த தொழில்நுட்பங்கள் எங்கள் இணையதளம் உங்கள் விருப்பங்களை நினைவில் வைத்து மேலும் தனிப்பயனாக்கப்பட்ட அனுபவத்தை வழங்க அனுமதிக்கின்றன.',
                'statistics_content' => 'இந்த தொழில்நுட்பங்கள் செயல்திறனை அளவிட்டு மேம்படுத்த இணையதள பயன்பாட்டை பகுப்பாய்வு செய்ய எங்களுக்கு உதவுகின்றன.',
                'marketing_content' => 'இந்த தொழில்நுட்பங்கள் எங்கள் சந்தைப்படுத்தல் கூட்டாளர்களால் உங்கள் ஆர்வங்களுக்கு பொருத்தமான தனிப்பயனாக்கப்பட்ட விளம்பரங்களைக் காட்ட பயன்படுத்தப்படுகின்றன.',
                'show_services' => 'சேவைகளைக் காட்டு',
                'hide_services' => 'சேவைகளை மறை',
                'modal_accept' => 'அனைத்தையும் ஏற்றுக்கொள்',
                'modal_reject' => 'அனைத்தையும் நிராகரி',
                'modal_save' => 'சேமி'
            ],

            // Telugu
            'te' => [
                'banner_text' => 'మా వెబ్‌సైట్‌లో మీ అనుభవాన్ని మెరుగుపరచడానికి మేము కుకీలు మరియు సారూప్య సాంకేతికతలను ఉపయోగిస్తాము.',
                'banner_link' => 'మా <a href="%s">గోప్యతా విధానాన్ని</a> చదవండి.',
                'button_accept' => 'అంగీకరించు',
                'button_reject' => 'తిరస్కరించు',
                'button_settings' => 'సెట్టింగ్‌లను నిర్వహించు',
                'modal_title' => 'గోప్యతా సెట్టింగ్‌లు',
                'modal_content' => 'ఈ వెబ్‌సైట్ కుకీలు మరియు సారూప్య సాంకేతికతలను ఉపయోగిస్తుంది. వాటిని మీరు క్రింద సమీక్షించి నిర్వహించగల వర్గాలుగా సమూహపరచారు. మీరు అనవసరమైన కుకీలను అంగీకరించినట్లయితే, సెట్టింగ్‌లలో ఎప్పుడైనా మీ ప్రాధాన్యతలను మార్చవచ్చు.',
                'modal_content_link' => 'మా <a href="%s">గోప్యతా విధానంలో</a> మరింత తెలుసుకోండి.',
                'functional_title' => 'ఫంక్షనల్',
                'preferences_title' => 'ప్రాధాన్యతలు',
                'statistics_title' => 'గణాంకాలు',
                'marketing_title' => 'మార్కెటింగ్',
                'functional_content' => 'మా వెబ్‌సైట్ యొక్క ప్రధాన కార్యాచరణను సక్రియం చేయడానికి ఈ సాంకేతికతలు అవసరం.',
                'preferences_content' => 'ఈ సాంకేతికతలు మా వెబ్‌సైట్ మీ ప్రాధాన్యతలను గుర్తుంచుకోవడానికి మరియు మరింత వ్యక్తిగతీకరించిన అనుభవాన్ని అందించడానికి అనుమతిస్తాయి.',
                'statistics_content' => 'ఈ సాంకేతికతలు పనితీరును కొలవడానికి మరియు మెరుగుపరచడానికి వెబ్‌సైట్ వినియోగాన్ని విశ్లేషించడానికి మాకు వీలు కల్పిస్తాయి.',
                'marketing_content' => 'ఈ సాంకేతికతలు మా మార్కెటింగ్ భాగస్వాములు మీ ఆసక్తులకు సంబంధించిన వ్యక్తిగతీకరించిన ప్రకటనలను చూపించడానికి ఉపయోగించబడతాయి.',
                'show_services' => 'సేవలను చూపు',
                'hide_services' => 'సేవలను దాచు',
                'modal_accept' => 'అన్నీ అంగీకరించు',
                'modal_reject' => 'అన్నీ తిరస్కరించు',
                'modal_save' => 'సేవ్ చేయి'
            ],

            // Marathi
            'mr' => [
                'banner_text' => 'आम्ही आमच्या वेबसाइटवर तुमचा अनुभव सुधारण्यासाठी कुकीज आणि तत्सम तंत्रज्ञान वापरतो.',
                'banner_link' => 'आमचे <a href="%s">गोपनीयता धोरण</a> वाचा.',
                'button_accept' => 'स्वीकारा',
                'button_reject' => 'नाकारा',
                'button_settings' => 'सेटिंग्ज व्यवस्थापित करा',
                'modal_title' => 'गोपनीयता सेटिंग्ज',
                'modal_content' => 'ही वेबसाइट कुकीज आणि तत्सम तंत्रज्ञान वापरते. ते खाली तुम्ही पुनरावलोकन आणि व्यवस्थापित करू शकता अशा श्रेणींमध्ये गटबद्ध आहेत. तुम्ही कोणतीही अनावश्यक कुकीज स्वीकारली असल्यास, तुम्ही सेटिंग्जमध्ये कधीही तुमची प्राधान्ये बदलू शकता.',
                'modal_content_link' => 'आमच्या <a href="%s">गोपनीयता धोरणात</a> अधिक जाणून घ्या.',
                'functional_title' => 'कार्यात्मक',
                'preferences_title' => 'प्राधान्ये',
                'statistics_title' => 'आकडेवारी',
                'marketing_title' => 'विपणन',
                'functional_content' => 'आमच्या वेबसाइटची मुख्य कार्यक्षमता सक्रिय करण्यासाठी या तंत्रज्ञानाची आवश्यकता आहे.',
                'preferences_content' => 'हे तंत्रज्ञान आमच्या वेबसाइटला तुमची प्राधान्ये लक्षात ठेवण्यास आणि अधिक वैयक्तिकृत अनुभव प्रदान करण्यास अनुमती देते.',
                'statistics_content' => 'हे तंत्रज्ञान आम्हाला कार्यक्षमता मोजण्यासाठी आणि सुधारण्यासाठी वेबसाइट वापराचे विश्लेषण करण्यास सक्षम करते.',
                'marketing_content' => 'हे तंत्रज्ञान आमच्या विपणन भागीदारांद्वारे तुमच्या आवडींशी संबंधित वैयक्तिकृत जाहिराती दाखवण्यासाठी वापरले जाते.',
                'show_services' => 'सेवा दाखवा',
                'hide_services' => 'सेवा लपवा',
                'modal_accept' => 'सर्व स्वीकारा',
                'modal_reject' => 'सर्व नाकारा',
                'modal_save' => 'जतन करा'
            ],

            // Swahili
            'sw' => [
                'banner_text' => 'Tunatumia vidakuzi na teknolojia zinazofanana kuboresha uzoefu wako kwenye tovuti yetu.',
                'banner_link' => 'Soma <a href="%s">Sera yetu ya Faragha</a>.',
                'button_accept' => 'Kubali',
                'button_reject' => 'Kataa',
                'button_settings' => 'Simamia Mipangilio',
                'modal_title' => 'Mipangilio ya Faragha',
                'modal_content' => 'Tovuti hii inatumia vidakuzi na teknolojia zinazofanana. Zimewekwa katika vikundi vya aina ambazo unaweza kupitia na kusimamia hapa chini. Ikiwa umekubali vidakuzi visivyo vya lazima, unaweza kubadilisha mapendeleo yako wakati wowote katika mipangilio.',
                'modal_content_link' => 'Jifunze zaidi katika <a href="%s">Sera yetu ya Faragha</a>.',
                'functional_title' => 'Kazi',
                'preferences_title' => 'Mapendeleo',
                'statistics_title' => 'Takwimu',
                'marketing_title' => 'Uuzaji',
                'functional_content' => 'Teknolojia hizi zinahitajika kuwezesha utendaji wa msingi wa tovuti yetu.',
                'preferences_content' => 'Teknolojia hizi huruhusu tovuti yetu kukumbuka mapendeleo yako na kukupa uzoefu wa kibinafsi zaidi.',
                'statistics_content' => 'Teknolojia hizi hutuwezesha kuchambua matumizi ya tovuti yetu ili kupima na kuboresha utendaji.',
                'marketing_content' => 'Teknolojia hizi hutumiwa na washirika wetu wa uuzaji kukuonyesha matangazo yaliyoboreshwa yanayohusiana na masilahi yako.',
                'show_services' => 'Onyesha Huduma',
                'hide_services' => 'Ficha Huduma',
                'modal_accept' => 'Kubali Zote',
                'modal_reject' => 'Kataa Zote',
                'modal_save' => 'Hifadhi'
            ],

            // Filipino/Tagalog
            'tl' => [
                'banner_text' => 'Gumagamit kami ng cookies at katulad na teknolohiya upang mapabuti ang iyong karanasan sa aming website.',
                'banner_link' => 'Basahin ang aming <a href="%s">Patakaran sa Privacy</a>.',
                'button_accept' => 'Tanggapin',
                'button_reject' => 'Tanggihan',
                'button_settings' => 'Pamahalaan ang Mga Setting',
                'modal_title' => 'Mga Setting ng Privacy',
                'modal_content' => 'Ang website na ito ay gumagamit ng cookies at katulad na teknolohiya. Nakagrupo ang mga ito sa mga kategorya na maaari mong suriin at pamahalaan sa ibaba. Kung tinanggap mo ang anumang hindi kinakailangang cookies, maaari mong baguhin ang iyong mga kagustuhan anumang oras sa mga setting.',
                'modal_content_link' => 'Matuto pa sa aming <a href="%s">Patakaran sa Privacy</a>.',
                'functional_title' => 'Functional',
                'preferences_title' => 'Mga Kagustuhan',
                'statistics_title' => 'Mga Istatistika',
                'marketing_title' => 'Marketing',
                'functional_content' => 'Ang mga teknolohiyang ito ay kinakailangan upang ma-activate ang pangunahing functionality ng aming website.',
                'preferences_content' => 'Ang mga teknolohiyang ito ay nagpapahintulot sa aming website na tandaan ang iyong mga kagustuhan at magbigay sa iyo ng mas personalized na karanasan.',
                'statistics_content' => 'Ang mga teknolohiyang ito ay nagbibigay-daan sa amin na suriin ang paggamit ng aming website upang masukat at mapabuti ang performance.',
                'marketing_content' => 'Ang mga teknolohiyang ito ay ginagamit ng aming mga kasosyo sa marketing upang ipakita sa iyo ang mga personalized na advertisement na may kaugnayan sa iyong mga interes.',
                'show_services' => 'Ipakita ang mga Serbisyo',
                'hide_services' => 'Itago ang mga Serbisyo',
                'modal_accept' => 'Tanggapin Lahat',
                'modal_reject' => 'Tanggihan Lahat',
                'modal_save' => 'I-save'
            ]
        ];
    }

    /**
     * Get string groups for admin UI organization
     *
     * @return array
     */
    public static function get_string_groups() {
        return [
            'banner' => [
                'label' => __('Banner', 'yt-consent-translations'),
                'keys' => ['banner_text', 'banner_link', 'button_accept', 'button_reject', 'button_settings']
            ],
            'modal' => [
                'label' => __('Modal', 'yt-consent-translations'),
                'keys' => ['modal_title', 'modal_content', 'modal_content_link']
            ],
            'categories' => [
                'label' => __('Categories', 'yt-consent-translations'),
                'keys' => [
                    'functional_title', 'functional_content',
                    'preferences_title', 'preferences_content',
                    'statistics_title', 'statistics_content',
                    'marketing_title', 'marketing_content'
                ]
            ],
            'buttons' => [
                'label' => __('Buttons', 'yt-consent-translations'),
                'keys' => ['show_services', 'hide_services', 'modal_accept', 'modal_reject', 'modal_save']
            ]
        ];
    }

    /**
     * Get human-readable label for a string key
     *
     * @param string $key String key
     * @return string
     */
    public static function get_key_label($key) {
        $labels = [
            'banner_text' => __('Banner Text', 'yt-consent-translations'),
            'banner_link' => __('Privacy Policy Link', 'yt-consent-translations'),
            'button_accept' => __('Accept Button', 'yt-consent-translations'),
            'button_reject' => __('Reject Button', 'yt-consent-translations'),
            'button_settings' => __('Settings Button', 'yt-consent-translations'),
            'modal_title' => __('Modal Title', 'yt-consent-translations'),
            'modal_content' => __('Modal Content', 'yt-consent-translations'),
            'modal_content_link' => __('Modal Privacy Link', 'yt-consent-translations'),
            'functional_title' => __('Functional Title', 'yt-consent-translations'),
            'preferences_title' => __('Preferences Title', 'yt-consent-translations'),
            'statistics_title' => __('Statistics Title', 'yt-consent-translations'),
            'marketing_title' => __('Marketing Title', 'yt-consent-translations'),
            'functional_content' => __('Functional Description', 'yt-consent-translations'),
            'preferences_content' => __('Preferences Description', 'yt-consent-translations'),
            'statistics_content' => __('Statistics Description', 'yt-consent-translations'),
            'marketing_content' => __('Marketing Description', 'yt-consent-translations'),
            'show_services' => __('Show Services', 'yt-consent-translations'),
            'hide_services' => __('Hide Services', 'yt-consent-translations'),
            'modal_accept' => __('Accept All Button', 'yt-consent-translations'),
            'modal_reject' => __('Reject All Button', 'yt-consent-translations'),
            'modal_save' => __('Save Button', 'yt-consent-translations')
        ];

        return isset($labels[$key]) ? $labels[$key] : $key;
    }

    /**
     * Check if a string key contains HTML placeholder
     *
     * @param string $key String key
     * @return bool
     */
    public static function has_placeholder($key) {
        $placeholders = ['banner_link', 'modal_content_link'];
        return in_array($key, $placeholders);
    }
}
