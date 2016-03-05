<?php

/**
 * Page SEO fields
 * Creates our page meta tags to deal with content, crawling, indexing, sitemap, and social sharing
 *
 * @package silverstripe-seo
 * @license MIT License https://github.com/Andrew-Mc-Cormack/Silverstripe-SEO/blob/master/LICENSE
 * @author  <andrewm@cyber-duck.co.uk>
 **/
class SEOExtension extends DataExtension {

    /**
     * @var string $title The CMS page SEO panel heading
     **/
    private $title = 'Meta Tags and SEO';

    /**
     * @var int $image_size The max image size for the social image
     **/
    private $image_size = 1024;

    /**
     * @var string $image_folder The social image folder
     **/
    private $image_folder = 'Social';

    /**
     * @static array $db Our page fields
     **/
    private static $db = array(
        'MetaTitle'       => 'Varchar(512)',
        'MetaDescription' => 'Varchar(512)',
        'Canonical'       => 'Varchar(512)',
        'Robots'          => 'Varchar(100)',
        'Priority'        => 'Decimal(3,2)',
        'ChangeFrequency' => 'Varchar(100)',
        'HideSocial'      => 'Boolean',
        'OGtype'          => 'Varchar(100)',
        'OGlocale'        => 'Varchar(10)',
        'TwitterCard'     => 'Varchar(100)',
    );

    /**
     * @static array $has_one Social image and other has_one relations
     **/
    private static $has_one = array(
        'SocialImage'     => 'Image'
    );

    /**
     * @static array $many_many Has many extra meta tags
     **/
    private static $many_many = array(
        'HeadTags'        => 'SEOHeadTag'
    );

    /**
     * @static array $defaults Sitemap defaults
     **/
    private static $defaults = array(
        'Priority'        => 0.50,
        'ChangeFrequency' => 'weekly'
    );
    
    /**
     * Adds our SEO meta fields to the page field list
     *
     * @return FieldList
     **/
    public function updateCMSFields(FieldList $fields) 
    {
        $fields->addFieldsToTab('Root.SEO', array(
            HeaderField::create($this->title),
            $this->preview(),
            TextField::create('MetaTitle'),
            TextareaField::create('MetaDescription'),
            HeaderField::create('Indexing'),
            TextField::create('Canonical'),
            DropdownField::create('Robots', 'Robots', $this->IndexRules()),
            HeaderField::create('Sitemap'),
            NumericField::create('Priority'),
            DropdownField::create('ChangeFrequency', 'Change Frequency', $this->SitemapChangeFrequency()),
            HeaderField::create('Social'),
            CheckboxField::create('HideSocial','Hide Social Meta?'),
            DropdownField::create('OGtype', 'Open Graph Type', $this->OGtype()),
            DropdownField::create('OGlocale', 'Open Graph Locale', $this->OGlocale()),
            DropdownField::create('TwitterCard', 'Twitter Card', $this->TwitterCardTypes()),
            $this->SharingImage(),
            $this->OtherHeadTags()
        ));

        return $fields;
    }

    private function preview()
    {
        return LiteralField::create('Preview', Controller::curr()->renderWith('MetaPreview'));
    }
    
    /**
     * Creates our social sharing upload field
     *
     * @return UploadField
     **/
    private function SharingImage()
    {
        $image = new UploadField('SocialImage');

        $image->getValidator()->setAllowedMaxFileSize($this->ImageSize());
        $image->setFolderName($this->image_folder);
        $image->setAllowedFileCategories('image');

        return $image;
    }
    
    /**
     * Creates our social sharing upload field
     *
     * @return UploadField
     **/
    private function OtherHeadTags()
    {
        $grid = new GridField(
            'HeadTags',
            'Other Meta Tags',
            $this->owner->HeadTags(),
            GridFieldConfig_RelationEditor::create()
        );
        $grid->getConfig()->removeComponentsByType('GridFieldAddExistingAutocompleter');
        return $grid;
    }
    
    /**
     * Returns the max upload image size
     *
     * @return Int
     **/
    private function ImageSize()
    {
        return $this->image_size * 1024;
    }
    
    /**
     * Returns an array of sitemap change frequencies used in a sitemap.xml file
     *
     * @return array
     **/
    private function SitemapChangeFrequency()
    {
        return array(
            'always'  => 'Always',
            'hourly'  => 'Hourly',
            'daily'   => 'Daily',
            'weekly'  => 'Weekly',
            'monthly' => 'Monthly',
            'yearly'  => 'Yearly',
            'never'   => 'Never'
        );
    }
    
    /**
     * Returns an array of robots crawling rules used in a robots meta tag
     *
     * @return array
     **/
    private function IndexRules()
    {
        return array(
            'index,follow'     => 'index,follow',
            'noindex,nofollow' => 'noindex,nofollow',
            'noindex,follow'   => 'noindex,follow',
            'index,nofollow'   => 'index,nofollow'
        );
    }
    
    /**
     * Return an array of Facebook Open Graph locales
     *
     * @return array
     **/
    private function OGlocale()
    {
        return array(
            'en_GB' => 'en_GB',
            'en_US' => 'en_US'
        );
    }
    
    /**
     * Return an array of Facebook Open Graph Types
     *
     * @return array
     **/
    private function OGtype()
    {
        return array(
            'website' => 'Website',
            'article' => 'Article',
            'book'    => 'Book',
            'profile' => 'Profile',
            'music'   => 'Music',
            'video'   => 'Video'
        );
    }
    
    /**
     * Returns an array of Twitter card types
     *
     * @return array
     **/
    private function TwitterCardTypes()
    {
        return array(
            'summary'             => 'Summary',
            'summary_large_image' => 'Summary Large Image',
            'photo'               => 'Photo',
            'gallery'             => 'Gallery',
            'app'                 => 'App',
            'product'             => 'Product'
        );
    }
}