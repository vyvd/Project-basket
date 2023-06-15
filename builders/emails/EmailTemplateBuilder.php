<?php

class EmailTemplateBuilder
{

    public const EMAIL_TEMPLATES_PATH = __DIR__ . '/../emails/templates';

    protected $account;
    protected $accountId;

    protected $currentTime;

    private $blockStyleOptions = [
        'text-decoration' => 'none',
        'color' => '#08586c',
        'background-color' => 'transparent',
        'border-radius' => '4px',
        'border-collapse' => 'initial',
        'border' => 'none',
        'padding-bottom' => '0',
        'font-family' => '\'Poppins, Helvetica\', ' . 'sans-serif',
        'text-align' => 'center',
        'word-break' => 'keep-all',
        'width' => '100%'
    ];
    private $blockContainerStyleOptions = [
        'margin-top' => '0px',
        'width' => '100%',
    ];

    protected $blockHeaderStyleOptions = [
        'color' => '#000000',
        'width' => '100%',
        'font-size' => '36px'
    ];


    protected $buttonLinkStyles = [
        'container' => [
            'background-color' => '#b2d489',
            'border-radius' => '10px',
            'padding-top' => '2px',
            'padding-bottom' => '4px',
            'padding-left' => '10px',
            'padding-right' => '10px',
            'text-align' => 'center',
            'v-text-anchor' => 'middle',
            'height' => '20px',
            'width' => '70px',
//            'display' => 'inline-block',
        ],
        'text' => [
            'color' => '#ffffff',
            'text-decoration' => 'none',
            'text-align' => 'center',
            'font-size' => '12px',
            'width' => 'auto',
            'display' => 'inline-block',
        ]
    ];

    public function __construct()
    {
        $this->setCurrentTime(time());
    }

    /**
     * @return ORM
     */
    public function getAccount(): ORM
    {
        return $this->account;
    }

    /**
     * @param ORM $account
     */
    public function setAccount(ORM $account): void
    {
        $this->account = $account;
        $this->setAccountId($this->account->get('id'));
    }

    /**
     * @return int
     */
    public function getAccountId(): int
    {
        return $this->accountId;
    }

    /**
     * @param int $accountId
     */
    public function setAccountId(int $accountId): void
    {
        $this->accountId = $accountId;
    }

    /**
     * @return int
     */
    public function getCurrentTime(): int
    {
        return $this->currentTime;
    }

    /**
     * @param int $currentTime
     */
    public function setCurrentTime(int $currentTime): void
    {
        $this->currentTime = $currentTime;
    }


    private function buildTemplatePathType(?string $type = null)
    {
        if ($type) {
            $type = "{$type}/";
        } else {
            $type = 'default/';
        }
        return $type;
    }

    public function fetchTemplate(string $name, array $config = [], ?string $type = null)
    {
        ob_start();
        include(self::EMAIL_TEMPLATES_PATH . "/{$this->buildTemplatePathType($type)}{$name}.php");
        return ob_get_clean();
    }

    public function buildStyleString(array $styleData)
    {
        $buildStyleOptionsData = [];
        foreach ($styleData as $key => $value) {
            $buildStyleOptionsData[] = "{$key}:$value";
        }
        return implode(';', $buildStyleOptionsData);
    }

    public function renderButtonLink($href, $text, $containerStyles = [], $textStyles = [])
    {
        $buttonStyles = $this->buttonLinkStyles;
        $buttonStyles['container'] = array_merge($this->buttonLinkStyles['container'], $containerStyles);
        $buttonStyles['text'] = array_merge($this->buttonLinkStyles['text'], $textStyles);
        $config = [
            'href' => $href,
            'text' => $text,
            'stylesData' => $buttonStyles
        ];
        return $this->fetchTemplate(
            'elements/button-link',
            $config
        );
    }
    public function renderButtonText($text, $containerStyles = [], $textStyles = [])
    {
        $buttonStyles = $this->buttonLinkStyles;
        $buttonStyles['container'] = array_merge($this->buttonLinkStyles['container'], $containerStyles);
        $buttonStyles['text'] = array_merge($this->buttonLinkStyles['text'], $textStyles);
        $config = [
            'text' => $text,
            'stylesData' => $buttonStyles
        ];
        return $this->fetchTemplate(
            'elements/button-text',
            $config
        );
    }

    public function renderBlockHeader(array $config = [], ?array $styles = [])
    {
        $blockHeaderConfig = [];
        if (isset($config['BLOCK_HEADER']) && is_array($config['BLOCK_HEADER'])) {
            $blockHeaderConfig = $config['BLOCK_HEADER'];
        }
        $blockHeaderConfig['styles'] = $this->buildStyleString(
            array_merge(
                $this->blockHeaderStyleOptions,
                $styles,
                (isset($blockHeaderConfig['styles']) && is_array($blockHeaderConfig['styles']))? $blockHeaderConfig['styles'] : []
            )
        );
        return $this->fetchTemplate(
            'block-grid/block-header',
            array_merge(
                ['styles' => $this->buildStyleString(array_merge($this->blockHeaderStyleOptions, $styles))],
                $blockHeaderConfig
            )
        );
    }

    public function buildBlockGrid(int $blocks, array $blockContentData, ?array $customStyleOptions = [], ?array $customBlockOptions = [])
    {
        if ($blocks > 10) {
            return false;
        }

        $blockColWidthClassName = "num" . (string)$blocks;
        $styleOptionsData = $this->blockStyleOptions;
        foreach ($customStyleOptions as $key => $customStyleOption) {
            $styleOptionsData[$key] = $customStyleOption;
        }
        $styleOptionsString = $this->buildStyleString($styleOptionsData);
        $blockOptionsData = array_merge($this->blockContainerStyleOptions, $customBlockOptions);
        $blockOptionsString = $this->buildStyleString(
            $blockOptionsData
        );

        $blockContents = '';
        foreach ($blockContentData as $index => $content) {
            $blockContents .= $this->fetchTemplate("block-grid/block",
                [
                    'COL_WIDTH' => $blockColWidthClassName,
                    'BLOCK_CONTENT' => $content,
                    'BLOCK_INNER_STYLE_OPTIONS' => $styleOptionsString
                ]
            );
        }

        return $this->fetchTemplate("block-grid/container",
            [
                'BLOCKS' => $blockContents,
                'BLOCK_CONTAINER_STYLE_OPTIONS' => $blockOptionsString

            ]
        );
    }

    public function buildList(array $listItemsData = [], ?array $customStyleOptions = [], ?array $customBlockOptions = [])
    {
        $listContent = '';
        foreach ($listItemsData as $content) {
            $listContent .= $this->fetchTemplate("sections/list/list-item",
                [
                    'LIST_ITEM_CONTENT' => $content,
                ]
            );
        }
        return $this->fetchTemplate("sections/list/list-wrapper",
            [
                'LIST_ITEMS' => $listContent
            ]
        );
    }

}