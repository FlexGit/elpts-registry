<?php

// Main
Breadcrumbs::for('main.index', function ($trail) {
    $trail->push('Главная', route('main.index'));
});

// Log
Breadcrumbs::for('log.index', function ($trail) {
    $trail->parent('main.index');
    $trail->push('Лог', route('log.index'));
});

// Docs
Breadcrumbs::for('docs.index', function ($trail, $doctype) {
    $trail->parent('main.index');
    $trail->push('Реестр документов "'.$doctype->name.'"', route('docs.index', $doctype));
});

Breadcrumbs::for('docs.show', function ($trail, $doctype, $docs) {
    $trail->parent('docs.index', $doctype);
    $trail->push('Документ "'.$docs['prefix_number'].'"', route('docs.show', $docs));
});
