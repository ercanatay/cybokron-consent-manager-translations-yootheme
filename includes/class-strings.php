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
        'en_US' => 'en',
        'en_GB' => 'en',
        'en_AU' => 'en',
        'en_CA' => 'en',
        'tr_TR' => 'tr',
        'hi_IN' => 'hi',
        'ko_KR' => 'ko',
        'ar' => 'ar',
        'ar_SA' => 'ar',
        'ar_AE' => 'ar',
        'ar_EG' => 'ar',
        'de_DE' => 'de',
        'de_AT' => 'de',
        'de_CH' => 'de',
        'de_DE_formal' => 'de'
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
