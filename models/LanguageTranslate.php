<?php

/**
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */

namespace lajax\translatemanager\models;

use Yii;

/**
 * This is the model class for table "language_translate".
 *
 * @property string  $id
 * @property string  $language
 * @property string  $translation
 * @property integer $is_translated
 *
 * @property LanguageSource $id0
 * @property Language $language0
 */
class LanguageTranslate extends \yii\db\ActiveRecord {

    /**
     * Status of inactive language.
     */
    const STATUS_NOT_TRANSLATED = 0;

    /**
     * Status of active language.
     */
    const STATUS_TRANSLATED = 1;

    /**
     * Array containing possible states.
     * @var array
     * @translate
     */
    private static $_CONDITIONS = [
        self::STATUS_NOT_TRANSLATED => 'Not translated',
        self::STATUS_TRANSLATED => 'Translated',
    ];

    /**
     * @var integer Number of translated language elements.
     */
    public $cnt;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        $dbMessageSources = Yii::getObjectVars(Yii::$app->i18n->getMessageSource('DbMessageSource'));
        return isset($dbMessageSources['messageTable']) ? $dbMessageSources['messageTable'] : '{{%message}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'language'], 'required'],
            [['id', 'is_translated'], 'integer'],
            [['id'], 'exist', 'targetClass' => '\lajax\translatemanager\models\LanguageSource'],
            [['language'], 'exist', 'targetClass' => '\lajax\translatemanager\models\Language', 'targetAttribute' => 'language_id'],
            [['translation'], 'string'],
            [['language'], 'string', 'max' => 5]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('model', 'ID'),
            'language' => Yii::t('model', 'Language'),
            'translation' => Yii::t('model', 'Translation'),
            'is_translated' => Yii::t('model', 'Is Translated'),
        ];
    }

    /**
     * Returns the state of the language (Active, Inactive or Beta) in the current language.
     * @return string
     */
    public function getStatusName() {
        return Yii::t('array', self::$_CONDITIONS[$this->is_translated]);
    }

    /**
     * Returns the names of possible states in an associative array.
     * @return array
     */
    public static function getStatusNames() {
        return \lajax\translatemanager\helpers\Language::a(self::$_CONDITIONS);
    }

    /**
     * Returnes language object by id and language_id. If not found, creates a new one.
     * @param integer $id LanguageSource id
     * @param string $languageId Language language_id
     * @return LanguageTranslate
     * @deprecated since version 1.2.7
     */
    public static function getLanguageTranslateByIdAndLanguageId($id, $languageId) {

        $languageTranslate = LanguageTranslate::findOne(['id' => $id, 'language' => $languageId]);
        if (!$languageTranslate) {
            $languageTranslate = new LanguageTranslate([
                'id' => $id,
                'language' => $languageId,
            ]);
        }

        return $languageTranslate;
    }

    /**
     * @return array The name of languages the language element has been translated into.
     */
    public function getTranslatedLanguageNames() {
        $translatedLanguages = $this->getTranslatedLanguages();

        $data = [];
        foreach ($translatedLanguages as $languageTranslate) {
            $data[$languageTranslate->language] = $languageTranslate->getLanguageName();
        }

        return $data;
    }

    /**
     * Returns the language element in all other languages.
     * @return LanguageTranslate
     */
    public function getTranslatedLanguages() {
        return static::find()->where('id = :id AND language != :language', [':id' => $this->id, 'language' => $this->language])->all();
    }

    /**
     * @staticvar array $language_names caching the list of languages.
     * @return string
     */
    public function getLanguageName() {
        static $language_names;
        if (!$language_names || empty($language_names[$this->language])) {
            $language_names = Language::getLanguageNames();
        }

        return empty($language_names[$this->language]) ? $this->language : $language_names[$this->language];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getId0() {
        return $this->hasOne(LanguageSource::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage0() {
        return $this->hasOne(Language::className(), ['language_id' => 'language']);
    }

}
