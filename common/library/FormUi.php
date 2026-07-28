<?php
namespace common\library;

use yii\helpers\Html;

/**
 * FormUi
 *
 * Centralised Tailwind class and Yii2 ActiveForm / DataTable config factory.
 *
 * Design system: BioSketch Professional — MD3 tokens, Inter / JetBrains Mono,
 * flat surfaces with `border-outline-variant`, no border-radius on inputs,
 * secondary (#006a61 teal) as the interactive accent.
 *
 * Split into two logical sections:
 *
 *   ① Form UI  — ActiveForm config, field templates, inputs, buttons, links
 *   ② Grid UI  — Table shell, th/td/tr classes, badges, chips, action buttons,
 *                stat chips, and CTA helpers for DataTables-powered views
 *
 * ┌──────────────────────────────────────────────────────────────────────┐
 * │  Design decisions                                                    │
 * │                                                                      │
 * │  • All methods are static — no instantiation needed.                 │
 * │  • Class strings use multi-line indentation for readability;         │
 * │    Tailwind's CDN JIT scans PHP strings so all classes resolve.      │
 * │  • badge() / chip() render full <span> HTML so views stay           │
 * │    expression-only: <?= FormUi::badge($label, $variant) ?>          │
 * │  • actionBtn() bakes in the delete confirmation + CSRF method        │
 * │    automatically when intent === 'delete'.                           │
 * │  • secondaryButton() mirrors the login "Create an account" pattern.  │
 * └──────────────────────────────────────────────────────────────────────┘
 */
class FormUi
{

    /* ══════════════════════════════════════════════════════════════════════
     │  ①  FORM UI
     ╚══════════════════════════════════════════════════════════════════════ */

    /*
    |--------------------------------------------------------------------------
    | ActiveForm config
    |--------------------------------------------------------------------------
    */

    /**
     * Top-level ActiveForm options.
     *
     * Usage:
     *   $form = ActiveForm::begin(FormUi::formConfig());
     *   $form = ActiveForm::begin(FormUi::formConfig('patient-form'));
     */
    public static function formConfig(string $id = 'auth-form', bool $isCreate = false): array
    {
        $isCreateClass = $isCreate ? 'px-sm md:px-md' : '';
        return [
            'id'      => $id,
            'options' => [
                'class' => 'space-y-md ' . $isCreateClass,
            ],
            /*
             * Global default field config. Override per-field with:
             *   $form->field($model, 'email', FormUi::fieldConfig('mail'))
             */
            'fieldConfig' => self::fieldConfig(),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Field config — with optional icon support
    |--------------------------------------------------------------------------
    */

    /**
     * Returns an ActiveField config array.
     *
     * When $icon is supplied the template wraps {input} in a relative
     * container with a right-anchored Material Symbol (decorative, pointer-none).
     * For interactive icons (e.g. password toggle) build the wrapper manually
     * in the view.
     *
     * @param string|null $icon  Material Symbols name, e.g. 'mail', 'lock',
     *                           'person', 'badge', 'phone'.
     *
     * Usage:
     *   $form->field($model, 'username', FormUi::fieldConfig())
     *   $form->field($model, 'email',    FormUi::fieldConfig('mail'))
     *   $form->field($model, 'password', FormUi::fieldConfig('lock'))
     */
    public static function fieldConfig(?string $icon = null): array
    {
        $template = $icon
            ? self::iconTemplate($icon)
            : "{label}\n{input}\n{error}";

        return [
            'template' => $template,

            'options' => [
                'class' => 'space-y-xs',
            ],

            'labelOptions' => [
                // Matches: <label class="font-label-caps text-label-caps text-on-surface-variant" for="...">
                'class' => 'font-label-caps text-label-caps text-on-surface-variant',
            ],

            'errorOptions' => [
                'class' => 'text-sm text-red-600 mt-1',
            ],
        ];
    }

    /**
     * Builds the Yii2 ActiveField template string for an icon field.
     *
     * Icon is right-anchored and decorative (pointer-events-none),
     * matching the login.html `<span class="material-symbols-outlined
     * absolute right-sm top-1/2 -translate-y-1/2 text-on-surface-variant">` pattern.
     *
     * For an interactive icon (password visibility toggle) build the
     * wrapper in the view and use a plain fieldConfig() instead.
     */
    private static function iconTemplate(string $icon): string
    {
        $iconHtml = Html::tag(
            'span',
            Html::encode($icon),
            ['class' => self::iconClass()]
        );

        $inputWrapper = Html::tag(
            'div',
            "\n{input}\n" . $iconHtml,
            ['class' => 'relative']
        );

        return "{label}\n" . $inputWrapper . "\n{error}";
    }


    /**
 * ActiveField config for password fields with an inline
 * "Forgot password?" link and an interactive visibility toggle.
 *
 * @param array  $forgotUrl  Yii2 URL array for the reset link.
 * @param string $toggleId   HTML id for the toggle button (allows
 *                           multiple password fields per page).
 */
public static function passwordFieldConfig(
    array  $forgotUrl = ['site/request-password-reset'],
    string $toggleId  = 'pwd-toggle'
): array {

    $forgotLink = Html::a('Forgot password?', $forgotUrl, [
        'class'    => self::linkClass(),
        'tabindex' => '-1',
    ]);

    $toggleBtn = Html::button(
        Html::tag('span', 'visibility', [
            'class' => 'material-symbols-outlined text-[20px]',
            'id'    => $toggleId . '-icon',
        ]),
        [
            'id'      => $toggleId,
            'type'    => 'button',
            'class'   => 'absolute right-sm top-1/2 -translate-y-1/2
                          text-on-surface-variant hover:text-primary
                          transition-colors',
            'encode'  => false,
            'aria-label' => 'Show password',
        ]
    );

    return [
        'template' =>
            '<div class="flex justify-between items-center mb-xs">'
          . '{label}'
          . $forgotLink
          . '</div>'
          . '<div class="relative">{input}' . $toggleBtn . '</div>'
          . "\n{error}",


        'options'      => ['class' => 'space-y-xs'],
        'labelOptions' => ['class' => 'font-label-caps text-label-caps text-on-surface-variant'],
        'errorOptions' => ['class' => 'text-sm text-red-600 mt-1'],
    ];
}


    /*
    |--------------------------------------------------------------------------
    | Input class
    |--------------------------------------------------------------------------
    */

    /**
     * Tailwind classes for <input> / <select> / <textarea> elements.
     *
     * Matches the BioSketch pattern:
     *   h-12 px-sm bg-surface-container-low border border-outline-variant
     *   focus:border-secondary focus:ring-1 focus:ring-secondary
     *   transition-all outline-none font-body-md text-on-surface
     *
     * Border-radius is intentionally omitted — the theme's DEFAULT radius
     * (0.125rem) is applied globally by Tailwind's base reset.
     *
     * @param bool $hasIcon  Adds right-padding to clear the decorative icon glyph.
     *
     * Usage:
     *   ->textInput(['class' => FormUi::inputClass()])
     *   ->textInput(['class' => FormUi::inputClass(true)])
     *   ->passwordInput(['class' => FormUi::inputClass()])
     */
    public static function inputClass(bool $hasIcon = false): string
    {
        $trailing = $hasIcon ? 'pr-sm' : 'pr-sm';   // keep symmetric; icon is absolute

        return "
            w-full
            h-12
            px-sm
            {$trailing}
            bg-surface-container-low
            border
            border-outline-variant
            focus:border-secondary
            focus:ring-1
            focus:ring-secondary
            transition-all
            outline-none
            font-body-md
            text-body-md
            text-on-surface
            placeholder:text-on-surface-variant/50
        ";
    }

  

    /**
     * Mono variant of the compact input (DOI fields, accession numbers, etc.).
     *
     * Usage:
     *   ->textInput(['class' => FormUi::inputClassMono()])
     */
    public static function inputClassMono(): string
    {
        return self::inputClassCompact() . ' font-data-mono text-data-mono text-[12px]';
    }


    /*
    |--------------------------------------------------------------------------
    | Icon class (decorative, right-anchored inside .relative wrapper)
    |--------------------------------------------------------------------------
    */

    /**
     * Tailwind classes for the Material Symbol span inside the input wrapper.
     *
     * Matches login.html:
     *   material-symbols-outlined absolute right-sm top-1/2 -translate-y-1/2
     *   text-on-surface-variant
     */
    public static function iconClass(): string
    {
        return '
            material-symbols-outlined
            absolute
            right-sm
            top-1/2
            -translate-y-1/2
            text-on-surface-variant
            pointer-events-none
        ';
    }


    /*
    |--------------------------------------------------------------------------
    | Primary submit / CTA button (full-width form variant)
    |--------------------------------------------------------------------------
    */

    /**
     * Tailwind classes for the full-width primary submit button.
     *
     * Matches login.html / create.html:
     *   w-full h-12 bg-primary text-on-primary font-headline-md text-[16px]
     *   font-bold hover:bg-primary-container active:scale-[0.98] transition-all
     *   flex items-center justify-center gap-sm
     *
     * Usage:
     *   Html::submitButton('Sign In', ['class' => FormUi::buttonClass()])
     */
    public static function buttonClass(): string
    {
        return '
            w-full
            h-12
            bg-primary
            text-on-primary
            font-headline-md
            text-[16px]
            font-bold
            hover:bg-primary-container
            active:scale-[0.98]
            transition-all
            flex
            items-center
            justify-center
            gap-sm
        ';
    }

    /**
     * Tailwind classes for the full-width secondary outline button.
     *
     * Matches login.html "Create an account" / create.html "Preview Biosketch":
     *   w-full h-12 border border-secondary text-secondary font-headline-md
     *   text-[16px] font-bold hover:bg-secondary-container
     *   hover:text-on-secondary-container transition-all flex items-center
     *   justify-center gap-sm
     *
     * Usage:
     *   Html::a('Preview', $url, ['class' => FormUi::buttonSecondaryClass()])
     */
    public static function buttonSecondaryClass(): string
    {
        return '
            w-full
            h-12
            bg-surface-container-lowest
            border
            border-secondary
            text-secondary
            font-headline-md
            text-[16px]
            font-bold
            hover:bg-secondary-container
            hover:text-on-secondary-container
            active:scale-[0.98]
            transition-all
            flex
            items-center
            justify-center
            gap-sm
        ';
    }

    private const BUTTON_SECONDARY_CREATE = '
    w-full
    py-4
    bg-white
    border
    border-secondary
    text-secondary
    font-bold
    rounded
    flex
    items-center
    justify-center
    gap-xs
';

    /**
     * Renders a full-width secondary outline <a> button.
     *
     * Usage:
     *   <?= FormUi::secondaryButton('Preview BioSketch', 'visibility', ['biosketch/preview', 'id' => $id]) ?>
     */
    public static function secondaryButton(string $label, string $icon, array $url): string
    {
        $inner = Html::tag('span', $icon, ['class' => 'material-symbols-outlined text-[20px]'])
               . Html::encode($label);

        return Html::a($inner, $url, [
            'class'  => self::buttonSecondaryClass(),
            'encode' => false,
        ]);
    }



    /*
    |--------------------------------------------------------------------------
    | Link helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Tailwind classes for inline anchor links.
     *
     * Matches login.html "Forgot password?":
     *   font-label-caps text-label-caps text-secondary hover:underline transition-all
     */
    public static function linkClass(): string
    {
        return '
            font-label-caps
            text-label-caps
            text-secondary
            hover:underline
            transition-all
        ';
    }

    /**
     * Renders a styled <a> tag.
     *
     * Usage:
     *   <?= FormUi::link('Back to Login', ['site/login']) ?>
     */
    public static function link(string $label, array $url, string $extraClass = ''): string
    {
        return Html::a($label, $url, [
            'class' => trim(self::linkClass() . ' ' . $extraClass),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Divider ("New to BioSketch?" separator)
    |--------------------------------------------------------------------------
    */

    /**
     * Renders a horizontal divider with a centred label.
     *
     * Matches login.html secondary-actions divider:
     *   h-[1px] bg-outline-variant, label font-label-caps text-label-caps
     *   text-on-surface-variant px-xs
     *
     * Usage:
     *   <?= FormUi::divider('New to BioSketch?') ?>
     *   <?= FormUi::divider('Or') ?>
     */
   public static function divider(string $label = 'Or'): string
{
    return '
        <div class="flex items-center gap-xs">
            <div class="h-px bg-outline-variant flex-1 self-center"></div>
            <span class="font-label-caps text-label-caps text-on-surface-variant whitespace-nowrap shrink-0 px-xs">'
                . Html::encode($label) . '
            </span>
            <div class="h-px bg-outline-variant flex-1 self-center"></div>
        </div>
    ';
}


    /*
    |--------------------------------------------------------------------------
    | Checkbox field config
    |--------------------------------------------------------------------------
    */

    /**
     * ActiveField config for checkbox fields.
     */
    public static function checkboxFieldConfig(): array
    {
        return [
            'template'     => "{input}\n{error}",
            'options'      => ['class' => 'mb-0'],
            'errorOptions' => ['class' => 'text-sm text-red-600 mt-2'],
        ];
    }

    /**
     * Options array for ->checkbox() calls.
     *
     * Matches login.html:
     *   w-4 h-4 border-outline-variant text-primary focus:ring-primary
     *   label: font-body-md text-body-md text-on-surface-variant
     *
     * Usage:
     *   $form->field($model, 'rememberMe', FormUi::checkboxFieldConfig())
     *        ->checkbox(FormUi::checkboxConfig('Keep me signed in for research continuity'))
     */
    public static function checkboxConfig(string $label): array
    {
        return [
            'label' => $label,

            'labelOptions' => [
                'class' => 'font-body-md text-body-md text-on-surface-variant select-none cursor-pointer',
            ],

            'class' => '
                w-4 h-4
                border-outline-variant
                text-primary
                focus:ring-primary
                bg-surface-container-lowest
            ',

            'container' => [
                'class' => 'flex items-center gap-sm py-xs',
            ],
        ];
    }


    /* ══════════════════════════════════════════════════════════════════════
     │  ②  GRID / DATATABLE UI
     ╚══════════════════════════════════════════════════════════════════════ */

    /*
    |--------------------------------------------------------------------------
    | Grid container shell
    |--------------------------------------------------------------------------
    */

    /**
     * Outer wrapper class for the DataTables grid panel.
     * Provides the card surface, border, and shadow.
     *
     * Usage:
     *   <div class="<?= FormUi::gridContainerClass() ?>">
     */
    public static function gridContainerClass(): string
    {
        return 'bg-surface-container-lowest border border-outline-variant overflow-hidden shadow-sm';
    }

    /**
     * Toolbar row above the table (export button slot + search input).
     *
     * Usage:
     *   <div class="<?= FormUi::gridToolbarClass() ?>">
     */
    public static function gridToolbarClass(): string
    {
        return 'px-sm md:px-md pt-md pb-xs '
             . 'flex flex-col sm:flex-row items-start sm:items-center justify-between gap-xs '
             . 'border-b border-outline-variant';
    }

    /**
     * Tailwind classes for the search <input> inside the toolbar.
     *
     * Usage:
     *   <input class="<?= FormUi::gridSearchClass() ?>" ... />
     */
    public static function gridSearchClass(): string
    {
        return 'w-full bg-surface-container border border-outline-variant '
             . 'py-2 pl-10 pr-sm '
             . 'font-body-md text-body-md text-on-surface '
             . 'focus:border-secondary focus:ring-1 focus:ring-secondary '
             . 'outline-none transition-all';
    }

    /**
     * Grid footer bar (DataTables info text + pagination row).
     *
     * Usage:
     *   <div class="<?= FormUi::gridFooterClass() ?>">
     */
    public static function gridFooterClass(): string
    {
        return 'bg-surface-container px-sm md:px-md py-xs '
             . 'flex flex-col sm:flex-row items-center justify-between gap-md '
             . 'border-t border-outline-variant';
    }


    /*
    |--------------------------------------------------------------------------
    | Table cell classes
    |--------------------------------------------------------------------------
    */

    /**
     * <th> header cell classes.
     *
     * @param bool $rightAlign  Pass true for the Actions column.
     *
     * Usage:
     *   <th class="<?= FormUi::thClass() ?>">Full Name</th>
     *   <th class="<?= FormUi::thClass(true) ?>">Actions</th>
     */
    public static function thClass(bool $rightAlign = false): string
    {
        return trim(
            'px-sm py-xs font-label-caps text-label-caps text-on-surface-variant '
          . 'uppercase tracking-widest whitespace-nowrap '
          . ($rightAlign ? 'text-right' : '')
        );
    }

    /**
     * <tr> body row classes — hover highlight.
     *
     * Usage:
     *   <tr class="<?= FormUi::trClass() ?>">
     */
    public static function trClass(): string
    {
        return 'bg-surface-container-lowest hover:bg-surface-container transition-colors border-b border-outline-variant/50';
    }

    /**
     * <td> body cell classes — three visual variants.
     *
     * @param string $variant
     *   'primary' — bold, black; use for IDs / key identifiers
     *   'muted'   — subdued on-surface-variant; use for dates and meta
     *   'mono'    — JetBrains Mono; use for accession numbers, DOIs
     *   'default' — standard body text
     *
     * Usage:
     *   <td class="<?= FormUi::tdClass('primary') ?>">PT-00042</td>
     *   <td class="<?= FormUi::tdClass('muted')   ?>">12 Jan 2024</td>
     *   <td class="<?= FormUi::tdClass('mono')    ?>">10.1038/s41593</td>
     *   <td class="<?= FormUi::tdClass()          ?>">Nairobi</td>
     */
    public static function tdClass(string $variant = 'default'): string
    {
        return 'px-sm py-xs ' . match($variant) {
            'primary' => 'font-headline-md font-bold text-primary text-body-md tracking-tight',
            'muted'   => 'font-body-md text-body-md text-on-surface-variant whitespace-nowrap',
            'mono'    => 'font-data-mono text-data-mono text-on-surface',
            default   => 'font-body-md text-body-md text-on-surface',
        };
    }


    /*
    |--------------------------------------------------------------------------
    | Badges and chips
    |--------------------------------------------------------------------------
    */

    /**
     * Renders a compact pill badge <span> — for status, category, or enum values.
     *
     * @param string $label    Display text (HTML-escaped internally).
     * @param string $variant  'secondary' | 'tertiary' | 'error' | 'warning' | 'default'
     *
     * Colour map (MD3 tokens):
     *   secondary → bg-secondary-container / text-on-secondary-container  (teal)
     *   tertiary  → bg-tertiary-fixed / text-on-tertiary-fixed-variant     (blue-slate)
     *   error     → bg-error-container / text-on-error-container           (red)
     *   warning   → amber tones
     *   default   → bg-surface-container / text-on-surface-variant        (neutral)
     *
     * Usage:
     *   <?= FormUi::badge('Stage IV', 'error') ?>
     *   <?= FormUi::badge('Active',   'secondary') ?>
     */
    public static function badge(string $label, string $variant = 'default'): string
    {
        $colors = match($variant) {
            'secondary' => 'bg-secondary-container text-on-secondary-container',
            'tertiary'  => 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
            'error'     => 'bg-error-container text-on-error-container',
            'warning'   => 'bg-[#fff3cd] text-[#7a5c00]',
            default     => 'bg-surface-container text-on-surface-variant',
        };

        return Html::tag('span', Html::encode($label), [
            'class' => "inline-flex items-center px-2.5 py-0.5 rounded-full font-label-caps text-[10px] font-bold {$colors}",
        ]);
    }

    /**
     * Renders a wider pill chip — for taxonomy / category labels.
     *
     * @param string $label
     * @param string $variant  Same variants as badge().
     *
     * Usage:
     *   <?= FormUi::chip('C50.9 — Breast, NOS') ?>
     *   <?= FormUi::chip('Nairobi', 'secondary') ?>
     */
    public static function chip(string $label, string $variant = 'default'): string
    {
        $colors = match($variant) {
            'secondary' => 'bg-secondary-container text-on-secondary-container',
            'tertiary'  => 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
            'error'     => 'bg-error-container text-on-error-container',
            'warning'   => 'bg-[#fff3cd] text-[#7a5c00]',
            default     => 'bg-surface-container text-on-surface',
        };

        return Html::tag('span', Html::encode($label), [
            'class' => "inline-flex items-center px-xs py-0.5 rounded-full font-label-caps text-[10px] font-bold {$colors}",
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Action buttons (grid row: view / edit / delete)
    |--------------------------------------------------------------------------
    */

    /**
     * Renders a single icon action button for a grid row.
     *
     * Bakes in:
     *   • Hover colour + background per intent
     *   • CSRF method:post + JS confirm dialog when intent === 'delete'
     *
     * @param string $icon    Material Symbols icon name, e.g. 'visibility'.
     * @param array  $url     Yii2 URL array, e.g. ['view', 'id' => $model->id].
     * @param string $intent  'view' | 'edit' | 'delete'
     * @param array  $options Extra Html::a options.
     *
     * Usage:
     *   <?= FormUi::actionBtn('visibility',  ['view',   'id' => $m->id], 'view')   ?>
     *   <?= FormUi::actionBtn('edit_square', ['update', 'id' => $m->id], 'edit')   ?>
     *   <?= FormUi::actionBtn('delete',      ['delete', 'id' => $m->id], 'delete') ?>
     */
    public static function actionBtn(
        string $icon,
        array  $url,
        string $intent  = 'view',
        array  $options = []
    ): string {
        $intentClass = match($intent) {
            'edit'   => 'p-2 text-on-surface-variant hover:text-secondary hover:bg-surface-container transition-all',
            'delete' => 'p-2 text-on-surface-variant hover:text-error hover:bg-error-container transition-all',
            default  => 'p-2 text-on-surface-variant hover:text-primary hover:bg-surface-container transition-all',
        };

        $iconSpan = Html::tag('span', $icon, ['class' => 'material-symbols-outlined text-[20px]']);

        $defaults = ['class' => $intentClass, 'encode' => false];

        if ($intent === 'delete') {
            $defaults['data'] = [
                'confirm' => 'Are you sure you want to delete this record?',
                'method'  => 'post',
            ];
        }

        return Html::a($iconSpan, $url, array_merge($defaults, $options));
    }

    /**
     * Renders a condensed (smaller icon) action button for mobile card views.
     *
     * Usage:
     *   <?= FormUi::actionBtnSm('visibility', ['view', 'id' => $m->id]) ?>
     */
    public static function actionBtnSm(
        string $icon,
        array  $url,
        string $intent  = 'view',
        array  $options = []
    ): string {
        $intentClass = match($intent) {
            'edit'   => 'p-1.5 text-on-surface-variant hover:text-secondary',
            'delete' => 'p-1.5 text-on-surface-variant hover:text-error',
            default  => 'p-1.5 text-on-surface-variant hover:text-primary',
        };

        $iconSpan = Html::tag('span', $icon, ['class' => 'material-symbols-outlined text-[16px]']);
        $defaults = ['class' => $intentClass, 'encode' => false];

        if ($intent === 'delete') {
            $defaults['data'] = [
                'confirm' => 'Are you sure you want to delete this record?',
                'method'  => 'post',
            ];
        }

        return Html::a($iconSpan, $url, array_merge($defaults, $options));
    }


    /*
    |--------------------------------------------------------------------------
    | CTA page-header button ("New Patient", "New Abstract", etc.)
    |--------------------------------------------------------------------------
    */

    /**
     * Renders the primary "New …" button used in page headers.
     *
     * Matches create.html save button style: bg-primary text-on-primary
     * font-bold rounded shadow-md active:scale-[0.98]
     *
     * @param string $label  Visible text, e.g. 'New Patient'.
     * @param string $icon   Material Symbol name, e.g. 'add', 'person_add'.
     * @param array  $url    Yii2 URL array.
     *
     * Usage:
     *   <?= FormUi::ctaButton('New Patient', 'person_add', ['patient/create']) ?>
     */
    public static function ctaButton(string $label, string $icon, array $url): string
    {
        $inner = Html::tag('span', $icon, ['class' => 'material-symbols-outlined text-[20px]'])
               . Html::tag('span', Html::encode($label));

        return Html::a($inner, $url, [
            'class'  => 'inline-flex items-center justify-center gap-xs px-md py-xs '
                      . 'bg-primary text-on-primary '
                      . 'font-label-caps text-label-caps '
                      . 'shadow-md '
                      . 'hover:bg-primary-container active:scale-[0.98] transition-all '
                      . 'w-full sm:w-auto',
            'encode' => false,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Stat chip — summary metric cards above the grid
    |--------------------------------------------------------------------------
    */

    /**
     * Renders a stat chip card (icon circle + label + value).
     *
     * @param string $icon       Material Symbol name, e.g. 'group'.
     * @param string $label      Small uppercase descriptor, e.g. 'Total Patients'.
     * @param string $value      Display value, e.g. '12,842'.
     * @param string $iconBg     Tailwind bg class for the icon circle.
     * @param string $iconColor  Tailwind text class for the icon.
     *
     * Usage:
     *   <?= FormUi::statChip('group', 'Total Patients', number_format($count)) ?>
     *   <?= FormUi::statChip(
     *         'pending_actions', 'Pending Review', '48',
     *         'bg-tertiary-fixed', 'text-on-tertiary-fixed-variant'
     *       ) ?>
     */
    public static function statChip(
        string $icon,
        string $label,
        string $value,
        string $iconBg    = 'bg-secondary-container',
        string $iconColor = 'text-on-secondary-container'
    ): string {
        $circle = Html::tag(
            'div',
            Html::tag('span', $icon, ['class' => 'material-symbols-outlined']),
            ['class' => "h-10 w-10 rounded-full {$iconBg} flex items-center justify-center {$iconColor} shrink-0"]
        );

        $text = Html::tag('div',
            Html::tag('p', Html::encode($label),
                ['class' => 'font-label-caps text-[10px] text-on-surface-variant uppercase tracking-widest'])
          . Html::tag('p', Html::encode($value),
                ['class' => 'font-headline-md text-headline-md font-bold text-primary']),
        []);

        return Html::tag('div', $circle . $text, [
            'class' => 'bg-surface-container-lowest p-sm border border-outline-variant flex items-center gap-sm',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Breadcrumb helper
    |--------------------------------------------------------------------------
    */

    /**
     * Renders a minimal breadcrumb trail.
     *
     * @param array $crumbs  [ 'Database', 'Patients' ] — last item is styled as active.
     *
     * Usage:
     *   <?= FormUi::breadcrumb(['Database', 'Patients']) ?>
     */
    public static function breadcrumb(array $crumbs): string
    {
        $parts = [];
        $last  = array_pop($crumbs);

        foreach ($crumbs as $crumb) {
            $parts[] = Html::tag('span', Html::encode($crumb), ['class' => 'text-on-surface-variant']);
            $parts[] = Html::tag('span', 'chevron_right', [
                'class' => 'material-symbols-outlined text-[12px] text-on-surface-variant',
            ]);
        }

        $parts[] = Html::tag('span', Html::encode($last), ['class' => 'text-primary']);

        return Html::tag('nav', implode('', $parts), [
            'class' => 'flex items-center gap-xs font-label-caps text-label-caps text-on-surface-variant mb-xs uppercase tracking-widest',
        ]);
    }

    // ==================================================================
    // NEW HELPERS FOR create.html (added without modifying anything above)
    // ==================================================================

    /**
     * Standard input class for create.html main fields.
     * Matches: bg-surface-container-low, border, rounded, p-2, form-focus-ring
     */
    public static function inputClassStandard(): string
    {
        return self::INPUT_STANDARD;
    }

    /**
     * Compact input class for publication entries (white background).
     */
    public static function inputClassCompact(): string
    {
        return self::INPUT_COMPACT;
    }

    /**
     * Textarea class (same as standard, adds resize-none).
     */
    public static function textareaClass(): string
    {
        return self::INPUT_STANDARD . ' resize-none';
    }

    /**
     * Select class (same as standard input).
     */
    public static function selectClass(): string
    {
        return self::INPUT_STANDARD;
    }

    /**
     * Renders the photo upload widget from create.html.
     * Generates a clickable circle that opens a hidden file input and shows preview.
     *
     * @param string $inputName   Name attribute for the file input.
     * @param string $currentImage Existing image URL (optional).
     * @return string
     */
    public static function photoUploadWidget(string $inputName = 'photo', string $currentImage = ''): string
    {
        $containerId = 'photo-upload-' . uniqid();
        $fileInputId = 'photo-file-' . uniqid();
        $previewId   = 'photo-preview-' . uniqid();
        $circleClass = self::UPLOAD_CIRCLE_CLASS;
        $bgStyle = $currentImage ? "background-image: url('" . Html::encode($currentImage) . "'); background-size: cover; background-position: center;" : '';

        return <<<HTML
<div id="{$containerId}" class="flex flex-col items-center gap-xs mb-sm">
    <div class="{$circleClass}" style="{$bgStyle}" onclick="document.getElementById('{$fileInputId}').click();">
        <span class="material-symbols-outlined text-3xl" data-icon="add_a_photo">add_a_photo</span>
        <span class="text-[10px] font-bold uppercase mt-1">Upload Photo</span>
    </div>
    <input type="file" id="{$fileInputId}" name="{$inputName}" accept="image/*" style="display: none;" onchange="previewPhoto(this, '{$previewId}', '{$containerId}')">
    <div id="{$previewId}"></div>
</div>

<script>
function previewPhoto(input, previewContainerId, containerId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var container = document.getElementById(containerId);
            if (container) {
                var uploadDiv = container.querySelector('.w-24.h-24');
                if (uploadDiv) {
                    uploadDiv.style.backgroundImage = "url('" + e.target.result + "')";
                    uploadDiv.style.backgroundSize = "cover";
                    uploadDiv.style.backgroundPosition = "center";
                    var iconSpan = uploadDiv.querySelector('.material-symbols-outlined');
                    var textSpan = uploadDiv.querySelector('.text-\\[10px\\]');
                    if (iconSpan) iconSpan.style.display = 'none';
                    if (textSpan) textSpan.style.display = 'none';
                }
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
HTML;
    }

    // ==================================================================
    // TAILWIND CLASS CONSTANTS – only for the new create.html helpers
    // (Existing methods keep their inline class strings)
    // ==================================================================

    private const INPUT_STANDARD = '
        w-full
        bg-surface-container-low
        border
        border-outline-variant
        rounded
        p-2
        text-body-md
        font-body-md
        form-focus-ring
    ';

    private const INPUT_COMPACT = '
        w-full
        bg-white
        border
        border-outline-variant
        rounded
        p-2
        text-body-md
        font-body-md
        form-focus-ring
    ';

    private const INPUT_MONO = '
        w-full
        bg-white
        border
        border-outline-variant
        rounded
        p-2
        font-data-mono
        text-data-mono
        text-[12px]
        form-focus-ring
    ';

    private const UPLOAD_CIRCLE_CLASS = '
       w-24 h-24 rounded-full bg-surface-container-high border-2 border-dashed border-outline-variant flex flex-col items-center justify-center text-on-surface-variant cursor-pointer hover:bg-surface-variant transition-colors
    ';

    private const SECTION_CLASS = 'bg-surface-container-lowest border border-outline-variant rounded p-sm space-y-sm p-md ';


    /**
 * Begins a repeatable section container with a header.
 *
 * @param string $title Section title (e.g., "Personal Information").
 * @param string $icon  Material Symbol name (e.g., "person", "school", "description").
 * @return string Opening HTML for the section.
 *
 * Usage:
 *   <?= FormUi::beginSection('Personal Information', 'person') ?>
 *       ... form fields ...
 *   <?= FormUi::endSection() ?>
 */
public static function beginSection(string $title, string $icon): string
{
    return '<section class="' . self::SECTION_CLASS . '">'
         . '<div class="flex items-center justify-between border-b border-outline-variant pb-xs mb-sm">'
         . '<h2 class="font-headline-md text-headline-md">' . Html::encode($title) . '</h2>'
         . '<span class="material-symbols-outlined text-on-surface-variant" data-icon="' . Html::encode($icon) . '">' . Html::encode($icon) . '</span>'
         . '</div>';
}

public static function endSection(): string
{
    return '</section>';
}

}