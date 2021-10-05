<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Forms\Block\Widget;

use CrazyCat\Forms\Model\Form as Model;
use CrazyCat\Forms\Model\FormFactory;
use CrazyCat\Forms\Model\ResourceModel\Form as ResourceModel;
use Magento\Directory\Model\ResourceModel\Country\Collection as CountryCollection;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use Magento\Directory\Model\TopDestinationCountries;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\Element\Template;
use Magento\ReCaptchaUi\Block\ReCaptcha;
use Magento\Widget\Block\BlockInterface;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_forms
 */
class Form extends Template implements BlockInterface
{
    /**
     * @var string|null
     */
    private $countryField = null;

    /**
     * @var CountryCollection\
     */
    private $countryCollection;

    /**
     * @var Model|null
     */
    private $form = null;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var RegionCollectionFactory
     */
    private $regionCollectionFactory;

    /**
     * @var string|null
     */
    private $regionField;

    /**
     * @var ResourceModel
     */
    private $resourceModel;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var TopDestinationCountries
     */
    private $topDestinationCountries;

    /**
     * @var EncoderInterface
     */
    private $urlEncoder;

    /**
     * @param CountryCollection       $countryCollection
     * @param FormFactory             $formFactory
     * @param RegionCollectionFactory $regionCollectionFactory
     * @param ResourceModel           $resourceModel
     * @param ScopeConfigInterface    $scopeConfig
     * @param TopDestinationCountries $topDestinationCountries
     * @param EncoderInterface        $urlEncoder
     * @param Template\Context        $context
     * @param array                   $data
     */
    public function __construct(
        CountryCollection $countryCollection,
        FormFactory $formFactory,
        RegionCollectionFactory $regionCollectionFactory,
        ResourceModel $resourceModel,
        ScopeConfigInterface $scopeConfig,
        TopDestinationCountries $topDestinationCountries,
        EncoderInterface $urlEncoder,
        Template\Context $context,
        array $data = []
    ) {
        $this->countryCollection = $countryCollection;
        $this->formFactory = $formFactory;
        $this->regionCollectionFactory = $regionCollectionFactory;
        $this->resourceModel = $resourceModel;
        $this->scopeConfig = $scopeConfig;
        $this->topDestinationCountries = $topDestinationCountries;
        $this->urlEncoder = $urlEncoder;
        parent::__construct($context, $data);
        $this->setTemplate('CrazyCat_Forms::form.phtml');
    }

    /**
     * Get form
     *
     * @return Model
     */
    public function getForm()
    {
        if ($this->form === null) {
            /** @var $form Model */
            $this->form = $this->formFactory->create();
            $this->resourceModel->load($this->form, $this->getDataByKey('identifier'), Model::FIELD_IDENTIFIER);
        }
        return $this->form;
    }

    /**
     * Get form action
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('forms/form/post', ['id' => $this->getForm()->getId()]);
    }

    /**
     * Get hidden input HTML
     *
     * @return string
     */
    public function getHiddenInputHtml()
    {
        return '<input type="hidden" name="' . ActionInterface::PARAM_NAME_URL_ENCODED . '" ' .
            'value="' . $this->urlEncoder->encode($this->_urlBuilder->getCurrentUrl()) . '"/>';
    }

    /**
     * Prepare data
     *
     * @param array $elementSources
     */
    protected function prepareData($elementSources)
    {
        foreach ($elementSources as $elementSource) {
            if ($elementSource[Model::ELEMENT_TYPE] == Model::ELEMENT_TYPE_COUNTRY) {
                $this->countryField = $elementSource[Model::ELEMENT_IDENTIFIER];
            } elseif ($elementSource[Model::ELEMENT_TYPE] == Model::ELEMENT_TYPE_REGION) {
                $this->regionField = $elementSource[Model::ELEMENT_IDENTIFIER];
            }
        }
    }

    /**
     * Get validation rules
     *
     * @param array $validations
     * @return array
     */
    protected function getValidationRules($validations)
    {
        return array_combine($validations, array_pad([], count($validations), true));
    }

    /**
     * Get region options array
     *
     * @return array
     */
    protected function getRegionOptionArray()
    {
        $options = [];
        $regionCollection = $this->regionCollectionFactory->create();
        foreach ($regionCollection as $region) {
            $options[] = [
                'label'      => $region->getDefaultName(),
                'value'      => $region->getId(),
                'country_id' => $region->getCountryId()
            ];
        }
        return $options;
    }

    /**
     * Get element config
     *
     * @param array  $elementSource
     * @param string $blockId
     * @return array
     */
    protected function initElement($elementSource, $blockId)
    {
        switch ($elementSource[Model::ELEMENT_TYPE]) {
            case Model::ELEMENT_TYPE_TEXT:
                $config = [
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'template'  => 'ui/form/field'
                ];
                break;

            case Model::ELEMENT_TYPE_DATE:
                $config = [
                    'component' => 'Magento_Ui/js/form/element/date',
                    'template'  => 'ui/form/field'
                ];
                break;

            case Model::ELEMENT_TYPE_SELECT:
                $config = [
                    'component' => 'Magento_Ui/js/form/element/select',
                    'template'  => 'ui/form/field'
                ];
                break;

            case Model::ELEMENT_TYPE_TEXTAREA:
                $config = [
                    'component' => 'Magento_Ui/js/form/element/textarea',
                    'template'  => 'ui/form/field'
                ];
                break;

            case Model::ELEMENT_TYPE_EDITOR:
                $config = [
                    'component' => 'Magento_Ui/js/form/element/wysiwyg',
                    'template'  => 'ui/form/field'
                ];
                break;

            case Model::ELEMENT_TYPE_COUNTRY:
                $this->scopeConfig;
                $config = [
                    'component' => 'Magento_Ui/js/form/element/country',
                    'template'  => 'ui/form/field',
                    'options'   => $this->countryCollection->loadByStore()->setForegroundCountries(
                        $this->topDestinationCountries->getTopDestinations()
                    )->toOptionArray()
                ];
                break;

            case Model::ELEMENT_TYPE_REGION:
                $config = [
                    'component'   => 'Magento_Ui/js/form/element/region',
                    'template'    => 'ui/form/field',
                    'options'     => $this->getRegionOptionArray(),
                    'customEntry' => 'data.' . $this->regionField,
                    'filterBy'    => [
                        'target' => '${$.provider}:${$.parentScope}.' . $this->countryField,
                        'field'  => 'country_id'
                    ],
                    'imports'     => [
                        'countryOptions' => '${$.parentName}.' . $this->countryField . ':indexedOptions',
                        'update'         => '${$.parentName}.' . $this->countryField . ':value'
                    ]
                ];
                break;

            case Model::ELEMENT_TYPE_POSTCODE:
                $config = [
                    'component' => 'Magento_Ui/js/form/element/post-code',
                    'template'  => 'ui/form/field',
                    'imports'   => [
                        'countryOptions' => '${$.parentName}.' . $this->countryField . ':indexedOptions',
                        'update'         => '${$.parentName}.' . $this->countryField . ':value'
                    ]
                ];
                break;

            default:
                $config = [];
        }

        return array_merge($config, [
            'dataScope'  => 'data.' . $elementSource[Model::ELEMENT_IDENTIFIER],
            'provider'   => $blockId . '.provider',
            'label'      => $elementSource[Model::ELEMENT_LABEL],
            'validation' => $this->getValidationRules($elementSource[Model::ELEMENT_VALIDATION] ?? []),
            'config'     => $elementSource[Model::ELEMENT_CONFIG]
                ? json_decode($elementSource[Model::ELEMENT_CONFIG], true) : []
        ]);
    }

    /**
     * Render elements
     */
    protected function renderElements()
    {
        $blockId = $this->getJsId();

        $elements = [];
        $elementSources = $this->getForm()->getDataByKey(Model::FIELD_ELEMENTS);
        $this->prepareData($elementSources);
        foreach ($elementSources as $elementSource) {
            $elements[$elementSource[Model::ELEMENT_IDENTIFIER]] = $this->initElement($elementSource, $blockId);
        }

        $this->jsLayout = [
            '*' => [
                'Magento_Ui/js/core/app' => [
                    'components' => [
                        $blockId => [ // namespace
                            'children' => [
                                'form-elements' => [
                                    'component' => 'uiCollection',
                                    'dataScope' => 'data',
                                    'children'  => $elements
                                ],
                                'provider'      => [
                                    'component'  => 'Magento_Ui/js/form/provider',
                                    'namespace'  => 'provider',
                                    'submit_url' => $this->getFormAction()
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        $formModel = $this->getForm();
        if ($formModel->getDataByKey(Model::FIELD_RENDERER) == Model::RENDERER_TEMPLATE) {
            $this->_template = $formModel->getDataByKey(Model::FIELD_TEMPLATE);
        } else {
            $this->renderElements();
        }
        return parent::_toHtml();
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->addChild('captcha', ReCaptcha::class, [
            'template'      => 'Magento_ReCaptchaFrontendUi::recaptcha.phtml',
            'recaptcha_for' => 'forms',
            'jsLayout'      => [
                'components' => [
                    'recaptcha' => [
                        'component' => 'Magento_ReCaptchaFrontendUi/js/reCaptcha'
                    ]
                ]
            ]
        ]);
        return $this;
    }
}
