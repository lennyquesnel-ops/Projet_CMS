(function () {
    'use strict';

    function debounce(callback, delay) {
        var timer = null;

        return function () {
            var args = arguments;
            var context = this;

            window.clearTimeout(timer);
            timer = window.setTimeout(function () {
                callback.apply(context, args);
            }, delay);
        };
    }

    function disableEnterSubmit(form) {
        if (!form || form.dataset.enterSubmitDisabled === '1') {
            return;
        }

        form.dataset.enterSubmitDisabled = '1';

        form.addEventListener('keydown', function (event) {
            if (event.key !== 'Enter') {
                return;
            }

            var target = event.target;
            var tagName = target && target.tagName ? target.tagName.toLowerCase() : '';

            if (tagName === 'textarea') {
                return;
            }

            if (
                target &&
                (
                    target.isContentEditable ||
                    target.closest('[contenteditable="true"]')
                )
            ) {
                return;
            }

            event.preventDefault();
        });
    }

    var VOID_TAGS = {
        area: true,
        base: true,
        br: true,
        col: true,
        embed: true,
        hr: true,
        img: true,
        input: true,
        link: true,
        meta: true,
        param: true,
        source: true,
        track: true,
        wbr: true
    };

    function cleanHtmlForEdition(content) {
        var html = (content || '').trim();

        html = html.replace(/<!doctype[^>]*>/gi, '');
        html = html.replace(/<head\b[^>]*>[\s\S]*?<\/head>/gi, '');
        html = html.replace(/<\/?html\b[^>]*>/gi, '');

        html = html.replace(/<style\b[^>]*data-grapesjs=["']true["'][^>]*>[\s\S]*?<\/style>/gi, '');
        html = html.replace(/<style\b[^>]*>[\s\S]*?<\/style>/gi, '');

        var bodyMatch = html.match(/<body\b[^>]*>([\s\S]*?)<\/body>/i);

        if (bodyMatch) {
            html = bodyMatch[1];
        } else {
            html = html.replace(/<\/?body\b[^>]*>/gi, '');
        }

        return html.trim();
    }

    function formatAttributes(element) {
        var attributes = [];

        for (var i = 0; i < element.attributes.length; i++) {
            var attribute = element.attributes[i];

            if (attribute.value === '') {
                attributes.push(attribute.name);
            } else {
                attributes.push(attribute.name + '="' + attribute.value.replace(/"/g, '&quot;') + '"');
            }
        }

        return attributes.length > 0 ? ' ' + attributes.join(' ') : '';
    }

    function formatNode(node, level) {
        var indent = '    '.repeat(level);
        var lines = [];

        if (node.nodeType === 3) {
            var text = node.textContent.replace(/\s+/g, ' ').trim();

            if (text !== '') {
                lines.push(indent + text);
            }

            return lines;
        }

        if (node.nodeType === 8) {
            var comment = node.textContent.trim();

            if (comment !== '') {
                lines.push(indent + '<!-- ' + comment + ' -->');
            }

            return lines;
        }

        if (node.nodeType !== 1) {
            return lines;
        }

        var tagName = node.tagName.toLowerCase();
        var attributes = formatAttributes(node);
        var children = Array.prototype.slice.call(node.childNodes).filter(function (child) {
            return !(child.nodeType === 3 && child.textContent.trim() === '');
        });

        if (VOID_TAGS[tagName]) {
            lines.push(indent + '<' + tagName + attributes + '>');
            return lines;
        }

        if (children.length === 0) {
            lines.push(indent + '<' + tagName + attributes + '></' + tagName + '>');
            return lines;
        }

        if (children.length === 1 && children[0].nodeType === 3) {
            var inlineText = children[0].textContent.replace(/\s+/g, ' ').trim();
            lines.push(indent + '<' + tagName + attributes + '>' + inlineText + '</' + tagName + '>');
            return lines;
        }

        lines.push(indent + '<' + tagName + attributes + '>');

        children.forEach(function (child) {
            lines = lines.concat(formatNode(child, level + 1));
        });

        lines.push(indent + '</' + tagName + '>');

        return lines;
    }

    function formatHtml(html) {
        html = cleanHtmlForEdition(html);

        if (html === '') {
            return '';
        }

        var template = document.createElement('template');
        template.innerHTML = html;

        var lines = [];

        Array.prototype.slice.call(template.content.childNodes).forEach(function (node) {
            lines = lines.concat(formatNode(node, 0));
        });

        return lines.join('\n').trim();
    }

    function splitSavedContent(content) {
        return {
            html: formatHtml(content),
            css: ''
        };
    }

    function buildSavedContent(editor) {
        return formatHtml(editor.getHtml());
    }

    function createEditorShell(textarea) {
        var shell = document.createElement('div');
        shell.className = 'atais-grapesjs-shell';

        var header = document.createElement('div');
        header.className = 'atais-grapesjs-header';
        header.innerHTML = '<strong>Éditeur visuel GrapesJS</strong><span>Glisse des blocs, clique sur un élément pour modifier son texte, son image ou son lien.</span>';

        var editorContainer = document.createElement('div');
        editorContainer.className = 'atais-grapesjs-editor';

        shell.appendChild(header);
        shell.appendChild(editorContainer);

        textarea.insertAdjacentElement('afterend', shell);
        textarea.classList.add('atais-grapesjs-hidden-textarea');

        return editorContainer;
    }

    function getDefaultBlocks() {
        return [
            {
                id: 'atais-section-title-text',
                category: 'Sections ATAIS',
                label: 'Titre + texte',
                content: '<section class="section border-0 m-0 py-5 bg-light"><div class="container py-5"><div class="row justify-content-center"><div class="col-lg-8 text-center"><p class="text-color-primary font-weight-bold text-2 text-uppercase mb-2">Sous-titre</p><h2 class="font-weight-bold text-color-dark text-9 mb-3">Titre de section</h2><p class="text-4 line-height-8 mb-0">Remplace ce texte par ton contenu.</p></div></div></div></section>'
            },
            {
                id: 'atais-two-columns',
                category: 'Sections ATAIS',
                label: '2 colonnes',
                content: '<section class="section border-0 m-0 py-5"><div class="container py-5"><div class="row align-items-center"><div class="col-lg-6 mb-4 mb-lg-0"><h2 class="font-weight-bold text-color-dark text-8 mb-3">Titre du bloc</h2><p class="text-4 line-height-8">Texte de présentation à remplacer.</p><a href="#" class="btn btn-primary btn-modern btn-rounded px-4 py-3">Bouton</a></div><div class="col-lg-6"><img src="/uploads/blocs/placeholder.jpg" alt="Image" class="img-fluid border-radius-2"></div></div></div></section>'
            },
            {
                id: 'atais-three-cards',
                category: 'Sections ATAIS',
                label: '3 cartes',
                content: '<section class="section border-0 m-0 py-5 bg-light"><div class="container py-5"><div class="row"><div class="col-md-6 col-lg-4 mb-4"><div class="card border-0 border-radius-2 h-100 overflow-hidden"><img src="/uploads/blocs/placeholder.jpg" alt="Image" class="img-fluid"><div class="card-body p-4"><p class="text-color-primary font-weight-bold text-2 text-uppercase mb-2">Catégorie</p><h3 class="font-weight-bold text-color-dark text-5">Titre</h3><p>Description courte du projet.</p></div></div></div><div class="col-md-6 col-lg-4 mb-4"><div class="card border-0 border-radius-2 h-100 overflow-hidden"><img src="/uploads/blocs/placeholder.jpg" alt="Image" class="img-fluid"><div class="card-body p-4"><p class="text-color-primary font-weight-bold text-2 text-uppercase mb-2">Catégorie</p><h3 class="font-weight-bold text-color-dark text-5">Titre</h3><p>Description courte du projet.</p></div></div></div><div class="col-md-6 col-lg-4 mb-4"><div class="card border-0 border-radius-2 h-100 overflow-hidden"><img src="/uploads/blocs/placeholder.jpg" alt="Image" class="img-fluid"><div class="card-body p-4"><p class="text-color-primary font-weight-bold text-2 text-uppercase mb-2">Catégorie</p><h3 class="font-weight-bold text-color-dark text-5">Titre</h3><p>Description courte du projet.</p></div></div></div></div></div></section>'
            },
            {
                id: 'atais-button',
                category: 'Éléments simples',
                label: 'Bouton',
                content: '<a href="#" class="btn btn-primary btn-modern btn-rounded px-5 py-3">Bouton</a>'
            },
            {
                id: 'atais-image',
                category: 'Éléments simples',
                label: 'Image',
                content: '<img src="/uploads/blocs/placeholder.jpg" alt="Image" class="img-fluid">'
            }
        ];
    }

    function addSourceEditorButton(editor, hiddenTextarea) {
        editor.Commands.add('atais-open-source-editor', function () {
            var modal = editor.Modal;

            var wrapper = document.createElement('div');
            wrapper.className = 'atais-source-modal';

            var help = document.createElement('p');
            help.className = 'atais-source-help';
            help.textContent = 'Modifie ici le HTML du bloc. Utilise les classes Bootstrap / Porto comme avant avec CKEditor. Le CSS généré par GrapesJS n’est pas sauvegardé.';
            var sourceTextarea = document.createElement('textarea');
            sourceTextarea.className = 'atais-source-textarea';
            sourceTextarea.value = buildSavedContent(editor);

            var actions = document.createElement('div');
            actions.className = 'atais-source-actions';

            var cancelButton = document.createElement('button');
            cancelButton.type = 'button';
            cancelButton.className = 'btn btn-secondary';
            cancelButton.textContent = 'Annuler';

            var applyButton = document.createElement('button');
            applyButton.type = 'button';
            applyButton.className = 'btn btn-primary';
            applyButton.textContent = 'Appliquer le code';

            cancelButton.addEventListener('click', function () {
                modal.close();
            });

            applyButton.addEventListener('click', function () {
                var parsedContent = splitSavedContent(sourceTextarea.value);

                editor.setComponents(parsedContent.html);
                editor.setStyle('');

                hiddenTextarea.value = buildSavedContent(editor);
                hiddenTextarea.dispatchEvent(new Event('input', { bubbles: true }));
                hiddenTextarea.dispatchEvent(new Event('change', { bubbles: true }));

                modal.close();
            });

            actions.appendChild(cancelButton);
            actions.appendChild(applyButton);

            wrapper.appendChild(help);
            wrapper.appendChild(sourceTextarea);
            wrapper.appendChild(actions);

            modal.setTitle('Modifier le code source du bloc');
            modal.setContent(wrapper);
            modal.open();

            window.setTimeout(function () {
                sourceTextarea.focus();
            }, 100);
        });

        editor.Panels.addButton('options', {
            id: 'atais-open-source-editor',
            className: 'fa fa-pencil-square-o',
            command: 'atais-open-source-editor',
            attributes: {
                title: 'Modifier le code source'
            }
        });
    }

    function initGrapesEditor(textarea) {
        if (textarea.dataset.grapesjsInitialized === '1') {
            return;
        }

        textarea.dataset.grapesjsInitialized = '1';

        if (typeof window.grapesjs === 'undefined') {
            console.error('GrapesJS n’est pas chargé.');
            return;
        }

        var editorContainer = createEditorShell(textarea);
        var savedContent = splitSavedContent(textarea.value);

        var canvasStyles = [
            textarea.dataset.grapesjsBootstrapCssUrl,
            textarea.dataset.grapesjsThemeCssUrl,
            textarea.dataset.grapesjsHelpersCssUrl
        ].filter(Boolean);

        var editor = window.grapesjs.init({
            container: editorContainer,
            height: '720px',
            width: 'auto',
            storageManager: false,
            noticeOnUnload: false,
            components: savedContent.html,
            style: '',
            canvas: {
                styles: canvasStyles
            },
            assetManager: {
                upload: textarea.dataset.grapesjsUploadUrl || false,
                uploadName: 'files',
                autoAdd: true,
                assets: []
            },
            blockManager: {
                blocks: getDefaultBlocks()
            },
            styleManager: {
                sectors: [
                    {
                        name: 'Dimension',
                        open: false,
                        properties: ['width', 'min-height', 'padding', 'margin']
                    },
                    {
                        name: 'Texte',
                        open: false,
                        properties: ['font-size', 'font-weight', 'line-height', 'text-align', 'color']
                    },
                    {
                        name: 'Décor',
                        open: false,
                        properties: ['background-color', 'border-radius', 'box-shadow']
                    }
                ]
            }
        });

        textarea.grapesjsEditor = editor;

        addSourceEditorButton(editor, textarea);

        var syncTextarea = debounce(function () {
            textarea.value = buildSavedContent(editor);
            textarea.dispatchEvent(new Event('input', { bubbles: true }));
            textarea.dispatchEvent(new Event('change', { bubbles: true }));
        }, 300);

        editor.on('update', syncTextarea);

        editor.on('asset:upload:error', function (error) {
            console.error('Erreur upload GrapesJS', error);
            alert('Impossible d’envoyer l’image. Vérifie le fichier ou les logs Symfony.');
        });

        editor.on('load', function () {
            var frame = editor.Canvas.getFrameEl();

            if (!frame || !frame.contentDocument) {
                return;
            }

            frame.contentDocument.addEventListener('click', function (event) {
                var link = event.target.closest ? event.target.closest('a[href]') : null;

                if (link) {
                    event.preventDefault();
                }
            }, true);
        });

        var assetsUrl = textarea.dataset.grapesjsAssetsUrl;

        if (assetsUrl) {
            fetch(assetsUrl, {
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(function (response) {
                    return response.ok ? response.json() : [];
                })
                .then(function (assets) {
                    if (Array.isArray(assets)) {
                        editor.AssetManager.add(assets);
                    }
                })
                .catch(function (error) {
                    console.error('Impossible de charger la médiathèque GrapesJS', error);
                });
        }

        var form = textarea.closest('form');

        if (form) {
            disableEnterSubmit(form);

            form.addEventListener('submit', function () {
                textarea.value = buildSavedContent(editor);
            });
        }
    }

    function initAllEditors() {
        document.querySelectorAll('textarea.js-grapesjs-editor').forEach(initGrapesEditor);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAllEditors);
    } else {
        initAllEditors();
    }
})();