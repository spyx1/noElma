@extends('layouts.app', ['title' => 'КВЦ'])

@section('content')
<style>
#kvc-tree-widget {
    width: 100%;
    height: 100%;
    min-height: 650px;
    font-family: inherit;
    color: #202735;
    box-sizing: border-box;
}

#kvc-tree-widget * {
    box-sizing: border-box;
}

/* =========================================================
   ВЕРХНЯЯ ПАНЕЛЬ — СТАНДАРТНЫЙ СТИЛЬ САЙТА
   ========================================================= */

#kvc-catalog-tools {
    width: auto;
    flex: 0 0 auto;
    margin-left: auto;
}

#kvc-catalog-tools .table-actions-menu button.active {
    background: #edf7f4;
    color: #0f766e;
}

#kvc-catalog-tools .kvc-view-check {
    margin-left: auto;
    opacity: 0;
}

#kvc-catalog-tools .table-actions-menu button.active .kvc-view-check {
    opacity: 1;
}

#kvc-catalog-tools .kvc-menu-separator {
    height: 1px;
    margin: 5px 0;
    background: #e3ebe8;
}

/* =========================================================
   VIEW
   ========================================================= */

#kvc-tree-widget .kvc-view {
    display: none;
}

#kvc-tree-widget .kvc-view.active {
    display: block;
}

/* =========================================================
   ДЕРЕВО
   ========================================================= */

#kvc-tree-widget .kvc-tree-view {
    width: 100%;
    min-height: 600px;
}

#kvc-tree-widget .kvc-scroll {
    position: relative;
    width: 100%;
    min-height: 600px;
    overflow: auto;

    border: 1px solid #dce6e3;
    border-radius: 10px;

    background-color: #ffffff;
    background-image:
        radial-gradient(
            circle,
            #e0e8e5 1px,
            transparent 1px
        );
    background-size: 22px 22px;
}

#kvc-tree-widget .kvc-canvas {
    position: relative;
    display: inline-block;
    min-width: 100%;
    min-height: 600px;
    width: max-content;
    padding: 40px 60px;
}

#kvc-tree-widget .kvc-svg {
    position: absolute;
    left: 0;
    top: 0;
    z-index: 1;
    pointer-events: none;
    overflow: visible;
}

#kvc-tree-widget .kvc-tree {
    position: relative;
    z-index: 2;
    width: max-content;
}

#kvc-tree-widget .kvc-branch {
    display: flex;
    align-items: center;
    width: max-content;
    gap: 80px;
}

#kvc-tree-widget .kvc-children {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 20px;
    width: max-content;
}

/* =========================================================
   ОРИЕНТАЦИЯ ДЕРЕВА
   Горизонтальное дерево — существующий вид слева направо.
   Вертикальное дерево — сверху вниз.
   ========================================================= */

#kvc-tree-widget .kvc-tree-view.kvc-tree-horizontal .kvc-branch {
    flex-direction: row;
    align-items: center;
}

#kvc-tree-widget .kvc-tree-view.kvc-tree-horizontal .kvc-children {
    flex-direction: column;
    justify-content: center;
}

/* В горизонтальном дереве высота области зависит от содержимого.
   Поэтому горизонтальная прокрутка находится сразу под блоками. */
#kvc-tree-widget .kvc-tree-view.kvc-tree-horizontal .kvc-scroll {
    min-height: 0;
}

#kvc-tree-widget .kvc-tree-view.kvc-tree-horizontal .kvc-canvas {
    min-height: 0;
}

#kvc-tree-widget .kvc-tree-view.kvc-tree-vertical .kvc-branch {
    flex-direction: column;
    align-items: center;
    gap: 70px;
}

#kvc-tree-widget .kvc-tree-view.kvc-tree-vertical .kvc-children {
    flex-direction: row;
    align-items: flex-start;
    justify-content: center;
    gap: 20px;
}

/* В вертикальном дереве flex-basis карточки не должен становиться её высотой.
   Размер карточки остаётся таким же, как в горизонтальном дереве. */
#kvc-tree-widget .kvc-tree-view.kvc-tree-vertical .kvc-card {
    flex-basis: auto;
}

#kvc-tree-widget .kvc-tree-view.kvc-tree-vertical .kvc-card.kvc {
    flex-basis: auto;
}

/* =========================================================
   КАРТОЧКА
   ========================================================= */

#kvc-tree-widget .kvc-card {
    position: relative;
    width: 305px;
    flex: 0 0 305px;

    background: #ffffff;

    border: 1px solid #d9dee7;
    border-radius: 10px;

    box-shadow:
        0 2px 5px rgba(20, 30, 50, 0.05),
        0 1px 2px rgba(20, 30, 50, 0.04);

    overflow: visible;

    transition:
        box-shadow 0.15s,
        border-color 0.15s,
        transform 0.15s;
}

#kvc-tree-widget .kvc-card:hover {
    transform: translateY(-1px);
    z-index: 10000;
    box-shadow:
        0 7px 18px rgba(20, 30, 50, 0.10);
}

#kvc-tree-widget .kvc-card.kvc {
    width: 320px;
    flex-basis: 320px;
    border: 2px solid #db6666;
}

#kvc-tree-widget .kvc-card.kvc-op {
    border-color: #e3a351;
}

#kvc-tree-widget .kvc-card.op {
    border-color: #6d96d8;
}

#kvc-tree-widget .kvc-card-top {
    display: block;
    width: 100%;
    padding: 10px 10px 7px 10px;
}

#kvc-tree-widget .kvc-top-line {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 7px;
}

#kvc-tree-widget .kvc-top-left {
    display: flex;
    align-items: center;
    gap: 6px;
    min-width: 0;
}

#kvc-tree-widget .kvc-drag-handle {
    position: relative;

    display: inline-block;

    width: 10px;
    height: 18px;

    flex: 0 0 10px;

    margin-right: 2px;
    padding: 0;

    border: 0;
    background: transparent;

    cursor: grab;
    user-select: none;
    touch-action: none;
}

#kvc-tree-widget .kvc-drag-handle::before {
    content: "";

    position: absolute;

    left: 1px;
    top: 3px;

    width: 3px;
    height: 3px;

    border-radius: 50%;

    background: #a7b1bf;

    box-shadow:
        5px 0 0 #a7b1bf,
        0 5px 0 #a7b1bf,
        5px 5px 0 #a7b1bf,
        0 10px 0 #a7b1bf,
        5px 10px 0 #a7b1bf;
}

#kvc-tree-widget .kvc-drag-handle:hover::before {
    background: #6f7c8e;

    box-shadow:
        5px 0 0 #6f7c8e,
        0 5px 0 #6f7c8e,
        5px 5px 0 #6f7c8e,
        0 10px 0 #6f7c8e,
        5px 10px 0 #6f7c8e;
}

#kvc-tree-widget .kvc-drag-handle:active {
    cursor: grabbing;
}

#kvc-tree-widget .kvc-card.kvc-drag-before::before,
#kvc-tree-widget .kvc-card.kvc-drag-after::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    height: 2px;
    z-index: 10020;
    background: #347bc1;
    border-radius: 2px;
    pointer-events: none;
}

#kvc-tree-widget .kvc-card.kvc-drag-before::before {
    top: -11px;
}

#kvc-tree-widget .kvc-card.kvc-drag-after::after {
    bottom: -11px;
}

#kvc-tree-widget .kvc-tree-view.kvc-tree-vertical .kvc-card.kvc-drag-before::before {
    left: -11px;
    right: auto;
    top: 0;
    bottom: 0;
    width: 2px;
    height: auto;
}

#kvc-tree-widget .kvc-tree-view.kvc-tree-vertical .kvc-card.kvc-drag-after::after {
    left: auto;
    right: -11px;
    top: 0;
    bottom: 0;
    width: 2px;
    height: auto;
}

#kvc-tree-widget .kvc-card.kvc-dragging {
    opacity: 0.55;
}

/* =========================================================
   BADGE
   ========================================================= */

#kvc-tree-widget .kvc-type-badge {
    display: inline-flex;
    align-items: center;
    flex: 0 0 auto;
    padding: 3px 6px;
    border-radius: 5px;
    font-size: 9px;
    line-height: 1;
    font-weight: 700;
}

#kvc-tree-widget .kvc-card.kvc .kvc-type-badge,
#kvc-tree-widget .kvc-table-badge.kvc {
    background: #fff1f1;
    color: #ca5050;
}

#kvc-tree-widget .kvc-card.kvc-op .kvc-type-badge,
#kvc-tree-widget .kvc-table-badge.kvc-op {
    background: #fff6e8;
    color: #ad7327;
}

#kvc-tree-widget .kvc-card.op .kvc-type-badge,
#kvc-tree-widget .kvc-table-badge.op {
    background: #eef5ff;
    color: #3f73bc;
}

/* =========================================================
   НАЗВАНИЕ
   ========================================================= */

#kvc-tree-widget .kvc-card-title {
    display: block;
    width: 100%;
    margin: 0;
    padding: 0;
    font-size: 13px;
    line-height: 1.35;
    font-weight: 700;
    color: #252d3d;
    white-space: normal;
    overflow-wrap: break-word;
}

/* =========================================================
   ИКОНКИ Laravel
   ========================================================= */

#kvc-tree-widget .kvc-icons {
    position: relative;
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}

#kvc-tree-widget .kvc-icon-wrap {
    position: relative;
    display: inline-flex;
}

#kvc-tree-widget .kvc-icon-button {
    width: 28px !important;
    min-width: 28px !important;
    max-width: 28px !important;

    height: 28px !important;
    min-height: 28px !important;
    max-height: 28px !important;

    padding: 0 !important;
    margin: 0 !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    border: 1px solid #cfd8e6 !important;
    border-radius: 50% !important;

    background: #ffffff !important;
    color: #5579a8 !important;

    box-shadow:
        0 1px 2px rgba(20, 30, 50, 0.04) !important;

    font-size: 14px !important;
    line-height: 1 !important;

    cursor: pointer;
}

#kvc-tree-widget .kvc-icon-button:hover,
#kvc-tree-widget .kvc-icon-button.active {
    color: #176fc1 !important;
    background: #f5f9fe !important;
    border-color: #9ebddd !important;
}

/* =========================================================
   POPUPS
   ========================================================= */

#kvc-tree-widget .kvc-popup {
    position: absolute;

    top: 36px;
    right: 0;

    z-index: 5000;

    width: 230px;
    padding: 12px;

    background: #ffffff;

    border: 1px solid #d9e0ea;
    border-radius: 10px;

    box-shadow:
        0 10px 30px rgba(28, 39, 58, 0.16),
        0 2px 6px rgba(28, 39, 58, 0.08);

    white-space: normal;
}

#kvc-tree-widget .kvc-popup::before {
    content: "";

    position: absolute;

    top: -5px;
    right: 9px;

    width: 9px;
    height: 9px;

    background: #ffffff;

    border-top: 1px solid #d9e0ea;
    border-left: 1px solid #d9e0ea;

    transform: rotate(45deg);
}

#kvc-tree-widget .kvc-popup-title {
    margin-bottom: 10px;

    font-size: 12px;
    font-weight: 700;

    color: #293345;
}

/* =========================================================
   ДОПОЛНИТЕЛЬНО
   ========================================================= */

#kvc-tree-widget .kvc-info-popup {
    width: 300px;
    z-index: 10000;
    left: 0;
    right: auto;
}

#kvc-tree-widget .kvc-info-popup::before {
    left: 9px;
    right: auto;
}

#kvc-tree-widget .kvc-info-text {
    max-height: 220px;

    overflow-y: auto;
    overflow-x: hidden;

    padding-right: 4px;

    font-size: 11px;
    line-height: 1.5;

    color: #465165;

    white-space: pre-wrap;
    overflow-wrap: break-word;
}

#kvc-tree-widget .kvc-info-empty {
    color: #929baa;
    font-style: italic;
}

#kvc-tree-widget .kvc-info-text::-webkit-scrollbar {
    width: 6px;
}

#kvc-tree-widget .kvc-info-text::-webkit-scrollbar-thumb {
    background: #d3dae4;
    border-radius: 10px;
}

/* =========================================================
   ПЕРИОД
   ========================================================= */

#kvc-tree-widget .kvc-date-popup {
    width: 285px;
}

#kvc-tree-widget .kvc-date-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
}

#kvc-tree-widget .kvc-date-row {
    min-width: 0;
    padding: 3px 10px 3px 0;
}

#kvc-tree-widget .kvc-date-row + .kvc-date-row {
    padding-left: 12px;
    padding-right: 0;
    border-left: 1px solid #e8ecf1;
}

#kvc-tree-widget .kvc-date-label {
    margin-bottom: 4px;
    font-size: 9px;
    color: #8a94a5;
    white-space: nowrap;
}

#kvc-tree-widget .kvc-date-value {
    font-size: 12px;
    font-weight: 600;
    color: #2c3749;
    white-space: nowrap;
}

#kvc-tree-widget .kvc-date-last-row {
    margin-top: 10px;
    padding-top: 9px;
    border-top: 1px solid #e8ecf1;
}

/* =========================================================
   УЧАСТНИКИ
   ========================================================= */

#kvc-tree-widget .kvc-users-popup {
    width: 255px;
}

#kvc-tree-widget .kvc-user-list {
    max-height: 210px;
    overflow-y: auto;
    overflow-x: hidden;
    padding-right: 4px;
}

#kvc-tree-widget .kvc-user-item {
    display: block;
    width: 100%;
    border-bottom: 1px solid #eef1f5;
}

#kvc-tree-widget .kvc-user-item:last-child {
    border-bottom: 0;
}

#kvc-tree-widget .kvc-user-link {
    display: block;
    width: 100%;
    padding: 7px 5px;
    color: #3476b9;
    font-size: 11px;
    line-height: 1.35;
    text-decoration: none;
    border-radius: 5px;
    cursor: pointer;
}

#kvc-tree-widget .kvc-user-link:hover {
    color: #1d63a7;
    background: #f4f7fb;
    text-decoration: underline;
}

#kvc-tree-widget .kvc-user-list::-webkit-scrollbar,
#kvc-tree-widget .kvc-owner-options::-webkit-scrollbar {
    width: 6px;
}

#kvc-tree-widget .kvc-user-list::-webkit-scrollbar-thumb,
#kvc-tree-widget .kvc-owner-options::-webkit-scrollbar-thumb {
    background: #d3dae4;
    border-radius: 10px;
}

/* =========================================================
   ОПИСАНИЕ НА КАРТОЧКЕ
   ========================================================= */

#kvc-tree-widget .kvc-goal {
    padding: 0 10px 8px 10px;

    font-size: 12px;
    line-height: 1.35;
    font-weight: 600;

    color: #2f394c;
}

/* =========================================================
   МЕТРИКИ
   ========================================================= */

#kvc-tree-widget .kvc-metrics {
    width: 100%;

    display: grid;

    grid-template-columns:
        repeat(3, minmax(0, 1fr));

    gap: 5px;

    padding: 4px 10px 9px 10px;
}

#kvc-tree-widget .kvc-metric {
    width: 100%;
    min-width: 0;

    height: 62px;

    padding: 5px 3px;

    display: flex;
    flex-direction: column;

    align-items: center;
    justify-content: center;

    text-align: center;

    border: 1px solid #e7ecf3;
    border-radius: 7px;

    background: #f7f9fc;

    overflow: hidden;
}

#kvc-tree-widget .kvc-metric-label {
    width: 100%;

    margin-bottom: 5px;

    font-size: 9px;
    line-height: 1;

    color: #687286;

    white-space: nowrap;

    overflow: hidden;
    text-overflow: ellipsis;
}

#kvc-tree-widget .kvc-metric-value {
    flex: 1 1 auto;

    width: 100%;
    min-width: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 15px;
    line-height: 1.08;
    font-weight: 700;

    text-align: center;

    white-space: normal;

    overflow-wrap: anywhere;

    overflow: hidden;
}

#kvc-tree-widget .kvc-metric-value.long {
    font-size: 13px;
}

#kvc-tree-widget .kvc-metric.plan .kvc-metric-value {
    color: #2b5fad;
}

#kvc-tree-widget .kvc-metric.fact .kvc-metric-value {
    color: #a16a00;
}

#kvc-tree-widget .kvc-metric.audit .kvc-metric-value {
    color: #2e8b57;
}

/* Факт: сравнение с рассчитанным планом */
#kvc-tree-widget .kvc-metric.fact.good {
    border-color: #8fd1a5;
    background: #f0faf4;
}

#kvc-tree-widget .kvc-metric.fact.good .kvc-metric-label,
#kvc-tree-widget .kvc-metric.fact.good .kvc-metric-value {
    color: #168047;
}

#kvc-tree-widget .kvc-metric.fact.bad {
    border-color: #f0a3a3;
    background: #fff3f3;
}

#kvc-tree-widget .kvc-metric.fact.bad .kvc-metric-label,
#kvc-tree-widget .kvc-metric.fact.bad .kvc-metric-value {
    color: #d92d2d;
}

#kvc-tree-widget .kvc-value-unit {
    margin-left: 3px;
    font-size: 0.62em;
    line-height: 1;
    font-weight: 500;
    opacity: 0.82;
    align-self: center;
}

/* =========================================================
   НИЗ КАРТОЧКИ
   ========================================================= */

#kvc-tree-widget .kvc-card-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 2px 10px 10px 10px;
}

#kvc-tree-widget .kvc-meetings-btn {
    height: 28px;
    padding: 0 9px;

    border: 1px solid #cfd6e3;
    border-radius: 6px;

    background: #ffffff;

    color: #2f394c;

    font-size: 10px;
    font-weight: 600;

    cursor: pointer;
}

#kvc-tree-widget .kvc-owner {
    min-width: 0;
    max-width: 175px;
    text-align: right;
}

#kvc-tree-widget .kvc-owner-label {
    margin-bottom: 2px;
    font-size: 9px;
    color: #8a93a3;
}

#kvc-tree-widget .kvc-owner-value {
    font-size: 10px;
    line-height: 1.25;
    font-weight: 600;
    color: #283449;
}

/* =========================================================
   + / -
   ========================================================= */

#kvc-tree-widget .kvc-toggle {
    position: absolute;

    right: -14px;
    top: 50%;

    transform: translateY(-50%);

    z-index: 30;

    width: 28px;
    height: 28px;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 0;

    border: 2px solid #ffffff;
    border-radius: 50%;

    background: #424b5d;

    color: #ffffff;

    box-shadow:
        0 2px 7px rgba(20, 30, 50, 0.25);

    font-size: 17px;

    cursor: pointer;
}

#kvc-tree-widget .kvc-tree-view.kvc-tree-vertical .kvc-toggle {
    left: 50%;
    right: auto;
    top: auto;
    bottom: -14px;

    transform: translateX(-50%);
}

/* =========================================================
   SVG
   ========================================================= */

#kvc-tree-widget .kvc-line {
    fill: none;
    stroke: #aeb7c6;
    stroke-width: 1.5;
    vector-effect: non-scaling-stroke;
}

#kvc-tree-widget .kvc-line-dot {
    fill: #ffffff;
    stroke: #9ea8b8;
    stroke-width: 1.5;
}

/* =========================================================
   ТАБЛИЦА
   ========================================================= */

#kvc-tree-widget .kvc-table-view {
    min-height: 600px;
    padding: 16px;
    background: #f8f9fb;
}

#kvc-tree-widget .kvc-table-box {
    width: 100%;
    overflow: auto;
    background: #ffffff;
    border: 1px solid #dde3eb;
    border-radius: 10px;

    box-shadow:
        0 2px 6px rgba(25, 35, 50, 0.05);
}

#kvc-tree-widget .kvc-table {
    width: 100%;
    min-width: 1100px;
    border-collapse: collapse;
    table-layout: fixed;
}

#kvc-tree-widget .kvc-table th {
    height: 42px;
    padding: 8px 10px;
    background: #f5f7fa;
    border-bottom: 1px solid #dfe5ed;
    color: #6c7687;
    font-size: 10px;
    font-weight: 600;
    text-align: left;
    white-space: nowrap;
}

#kvc-tree-widget .kvc-table td {
    height: 48px;
    padding: 7px 10px;
    border-bottom: 1px solid #edf0f4;
    font-size: 11px;
    color: #2e394b;
    vertical-align: middle;
}

#kvc-tree-widget .kvc-table tbody tr:last-child td {
    border-bottom: 0;
}

#kvc-tree-widget .kvc-table tbody tr:hover td {
    background: #fafcff;
}

#kvc-tree-widget .kvc-table-type {
    width: 90px;
}

#kvc-tree-widget .kvc-table-name {
    width: 310px;
}

#kvc-tree-widget .kvc-table-value {
    width: 105px;
}

#kvc-tree-widget .kvc-table-audit {
    width: 100px;
}

#kvc-tree-widget .kvc-table-owner {
    width: 190px;
}

#kvc-tree-widget .kvc-table-period {
    width: 160px;
}

#kvc-tree-widget .kvc-table-actions {
    width: 155px;
    text-align: right !important;
}

#kvc-tree-widget .kvc-table-badge {
    display: inline-flex;
    padding: 4px 7px;
    border-radius: 5px;
    font-size: 9px;
    font-weight: 700;
}

#kvc-tree-widget .kvc-table-name-wrap {
    display: flex;
    align-items: center;
    min-width: 0;
}

#kvc-tree-widget .kvc-table-tree-line {
    flex: 0 0 auto;
    width: 16px;
    height: 1px;
    margin-right: 6px;
    background: #c7cfda;
}

#kvc-tree-widget .kvc-table-name-text {
    min-width: 0;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

#kvc-tree-widget .kvc-table-level-0 {
    padding-left: 0;
}

#kvc-tree-widget .kvc-table-level-1 {
    padding-left: 18px;
}

#kvc-tree-widget .kvc-table-level-2 {
    padding-left: 38px;
}

#kvc-tree-widget .kvc-table-level-3 {
    padding-left: 58px;
}

#kvc-tree-widget .kvc-table-level-4 {
    padding-left: 78px;
}

#kvc-tree-widget .kvc-table-plan {
    color: #2b5fad;
    font-weight: 600;
}

#kvc-tree-widget .kvc-table-fact {
    color: #a16a00;
    font-weight: 600;
}

#kvc-tree-widget .kvc-table-fact.good {
    color: #168047;
    background: #f0faf4;
}

#kvc-tree-widget .kvc-table-fact.bad {
    color: #d92d2d;
    background: #fff3f3;
}

#kvc-tree-widget .kvc-table tbody tr:hover td.kvc-table-fact.good {
    background: #f0faf4;
}

#kvc-tree-widget .kvc-table tbody tr:hover td.kvc-table-fact.bad {
    background: #fff3f3;
}

#kvc-tree-widget .kvc-table-value-html {
    display: inline-flex;
    align-items: baseline;
}

#kvc-tree-widget .kvc-table-audit-value {
    color: #2e8b57;
    font-weight: 600;
}

#kvc-tree-widget .kvc-table-actions-wrap {
    display: flex;
    justify-content: flex-end;
    gap: 4px;
}

/* =========================================================
   MODAL
   ========================================================= */

#kvc-tree-widget .kvc-modal-overlay {
    position: fixed;

    inset: 0;

    z-index: 100000;

    display: none;

    align-items: center;
    justify-content: center;

    padding: 20px;

    background: rgba(25, 34, 49, 0.38);

    overflow: visible;
}

#kvc-tree-widget .kvc-modal-overlay.open {
    display: flex;
}

#kvc-tree-widget .kvc-modal {
    position: relative;

    width: 620px;

    max-width: calc(100vw - 40px);
    max-height: calc(100vh - 40px);

    display: flex;
    flex-direction: column;

    background: #ffffff;

    border-radius: 12px;

    box-shadow:
        0 20px 60px rgba(17, 27, 43, 0.25);

    overflow: hidden;
}

#kvc-tree-widget .kvc-modal.user-mode {
    width: 400px;
}

#kvc-tree-widget .kvc-modal-header {
    flex: 0 0 auto;

    display: flex;

    align-items: center;
    justify-content: space-between;

    padding: 16px 18px;

    border-bottom: 1px solid #e8ebf0;
}

#kvc-tree-widget .kvc-modal-title {
    font-size: 16px;
    font-weight: 700;

    color: #263143;
}

#kvc-tree-widget .kvc-modal-close {
    width: 30px;
    height: 30px;

    padding: 0;

    border: 0;
    border-radius: 50%;

    background: transparent;

    color: #748093;

    font-size: 22px;

    cursor: pointer;
}

#kvc-tree-widget .kvc-modal-close:hover {
    background: #f2f4f7;
}

#kvc-tree-widget .kvc-modal-body {
    flex: 1 1 auto;

    min-height: 0;

    overflow-y: auto;
    overflow-x: hidden;

    padding: 18px;
}

#kvc-tree-widget .kvc-admin-settings,
#kvc-tree-widget .kvc-user-settings {
    display: none;
}

#kvc-tree-widget .kvc-admin-settings.visible,
#kvc-tree-widget .kvc-user-settings.visible {
    display: block;
}

/* =========================================================
   ФОРМА
   ========================================================= */

#kvc-tree-widget .kvc-form-row {
    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 12px;

    margin-bottom: 14px;
}

#kvc-tree-widget .kvc-form-row:last-child {
    margin-bottom: 0;
}

#kvc-tree-widget .kvc-create-type-row {
    display: none;
}

#kvc-tree-widget .kvc-create-type-row.visible {
    display: grid;
}

#kvc-tree-widget .kvc-form-field {
    position: relative;
    min-width: 0;
}

#kvc-tree-widget .kvc-form-field.full {
    grid-column: 1 / -1;
}

#kvc-tree-widget .kvc-form-label {
    display: block;

    margin-bottom: 5px;

    font-size: 11px;
    font-weight: 600;

    color: #616b79;
}

#kvc-tree-widget .kvc-form-input,
#kvc-tree-widget .kvc-form-textarea,
#kvc-tree-widget .kvc-form-select {
    width: 100%;

    border: 1px solid #d4dae4;
    border-radius: 5px;

    background: #ffffff;

    color: #293448;

    font-family: inherit;
    font-size: 12px;

    outline: none;
}

#kvc-tree-widget .kvc-form-input,
#kvc-tree-widget .kvc-form-select {
    height: 36px;

    padding: 0 10px;
}

#kvc-tree-widget .kvc-form-select {
    cursor: pointer;
}

#kvc-tree-widget .kvc-form-textarea {
    min-height: 80px;
    height: 80px;

    padding: 9px 10px;

    resize: vertical;
}

#kvc-tree-widget .kvc-form-input:focus,
#kvc-tree-widget .kvc-form-textarea:focus,
#kvc-tree-widget .kvc-form-select:focus {
    border-color: #3986ce;

    box-shadow:
        0 0 0 1px rgba(57, 134, 206, 0.10);
}

#kvc-tree-widget .kvc-indicator-section {
    display: none;
}

#kvc-tree-widget .kvc-indicator-section.visible {
    display: grid;
}

/* =========================================================
   CURRENT VALUE
   ========================================================= */

#kvc-tree-widget .kvc-current-value-boolean {
    display: none;
}

#kvc-tree-widget .kvc-current-value-boolean.visible {
    display: block;
}

#kvc-tree-widget .kvc-current-value-text.hidden {
    display: none;
}

#kvc-tree-widget .kvc-current-text-wrap {
    position: relative;
    width: 100%;
}

#kvc-tree-widget .kvc-current-text-wrap.hidden {
    display: none;
}

#kvc-tree-widget .kvc-current-text-wrap.has-unit .kvc-form-input {
    padding-right: 95px;
}

#kvc-tree-widget .kvc-current-unit {
    position: absolute;

    right: 11px;
    top: 50%;

    transform: translateY(-50%);

    padding-left: 9px;

    color: #7b8594;

    font-size: 11px;
    font-weight: 500;

    line-height: 1;

    white-space: nowrap;

    pointer-events: none;
}

#kvc-tree-widget .kvc-current-unit::before {
    content: "";

    position: absolute;

    left: 0;
    top: 50%;

    width: 1px;
    height: 18px;

    transform: translateY(-50%);

    background: #e1e5eb;
}

/* =========================================================
   OWNER
   ========================================================= */

#kvc-tree-widget .kvc-owner-control {
    position: relative;

    width: 100%;
    height: 36px;

    display: flex;

    align-items: center;

    padding: 0 34px 0 10px;

    border: 1px solid #d4dae4;
    border-radius: 5px;

    background: #ffffff;

    color: #293448;

    font-size: 12px;

    cursor: pointer;
}

#kvc-tree-widget .kvc-owner-control::after {
    content: "";

    position: absolute;

    right: 12px;
    top: 13px;

    width: 6px;
    height: 6px;

    border-right: 1px solid #758092;
    border-bottom: 1px solid #758092;

    transform: rotate(45deg);
}

#kvc-tree-widget .kvc-owner-placeholder {
    color: #8a94a5;
}

#kvc-tree-widget .kvc-owner-dropdown {
    position: fixed;

    z-index: 100100;

    display: none;

    min-width: 250px;

    padding: 8px;

    background: #ffffff;

    border: 1px solid #d5dde8;
    border-radius: 7px;

    box-shadow:
        0 10px 30px rgba(25, 38, 58, 0.20),
        0 2px 6px rgba(25, 38, 58, 0.08);
}

#kvc-tree-widget .kvc-owner-dropdown.open {
    display: block;
}

#kvc-tree-widget .kvc-owner-search-wrap {
    position: relative;

    margin-bottom: 6px;
}

#kvc-tree-widget .kvc-owner-search {
    width: 100%;
    height: 32px;

    padding: 0 32px 0 9px;

    border: 1px solid #d4dae4;
    border-radius: 5px;

    background: #ffffff;

    font-family: inherit;
    font-size: 11px;

    outline: none;
}

#kvc-tree-widget .kvc-owner-search-icon {
    position: absolute;

    right: 9px;
    top: 50%;

    transform: translateY(-50%);

    font-size: 14px;

    color: #778397;
}

#kvc-tree-widget .kvc-owner-options {
    max-height: 220px;
    overflow-y: auto;
}

#kvc-tree-widget .kvc-owner-option {
    padding: 7px 8px;
    border-radius: 5px;
    font-size: 11px;
    cursor: pointer;
}

#kvc-tree-widget .kvc-owner-option:hover {
    background: #f1f5fa;
    color: #246caf;
}


/* =========================================================
   ПРАВА НА СОБРАНИЯ
   ========================================================= */

#kvc-tree-widget .kvc-meeting-managers-row {
    display: grid;
}

#kvc-tree-widget .kvc-meeting-managers-row.hidden {
    display: none;
}

#kvc-tree-widget .kvc-meeting-managers-control {
    position: relative;
    width: 100%;
}

/* Поле в стиле Select2 multiple: выбранные пользователи видны тегами,
   а полный список открывается только по клику. */
#kvc-tree-widget .kvc-meeting-managers-selection {
    position: relative;

    width: 100%;
    min-height: 36px;

    display: flex;
    align-items: center;

    padding: 4px 34px 4px 6px;

    border: 1px solid #d4dae4;
    border-radius: 6px;

    background: #ffffff;

    cursor: pointer;
    outline: none;
}

#kvc-tree-widget .kvc-meeting-managers-selection:hover {
    border-color: #b9c4d2;
}

#kvc-tree-widget .kvc-meeting-managers-selection.open,
#kvc-tree-widget .kvc-meeting-managers-selection:focus {
    border-color: #3986ce;

    box-shadow:
        0 0 0 1px rgba(57, 134, 206, 0.10);
}

#kvc-tree-widget .kvc-meeting-managers-selection::after {
    content: "";

    position: absolute;

    right: 12px;
    top: 50%;

    width: 6px;
    height: 6px;

    border-right: 1px solid #758092;
    border-bottom: 1px solid #758092;

    transform: translateY(-65%) rotate(45deg);

    transition: transform 0.15s;
}

#kvc-tree-widget .kvc-meeting-managers-selection.open::after {
    transform: translateY(-30%) rotate(225deg);
}

#kvc-tree-widget .kvc-meeting-managers-tags {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;

    min-width: 0;
}

#kvc-tree-widget .kvc-meeting-managers-placeholder {
    padding: 3px 4px;

    color: #8a94a5;

    font-size: 11px;
    line-height: 1.25;
}

#kvc-tree-widget .kvc-meeting-manager-tag {
    max-width: 220px;

    display: inline-flex;
    align-items: center;
    gap: 5px;

    min-height: 25px;

    padding: 3px 6px 3px 8px;

    border: 1px solid #c8d9eb;
    border-radius: 5px;

    background: #eef6ff;

    color: #2d6398;

    font-size: 10px;
    line-height: 1.25;
}

#kvc-tree-widget .kvc-meeting-manager-tag-name {
    min-width: 0;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

#kvc-tree-widget .kvc-meeting-manager-tag-remove {
    width: 16px;
    height: 16px;

    flex: 0 0 16px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    padding: 0;

    border: 0;
    border-radius: 50%;

    background: transparent;

    color: #6683a0;

    font-family: Arial, sans-serif;
    font-size: 14px;
    line-height: 1;

    cursor: pointer;
}

#kvc-tree-widget .kvc-meeting-manager-tag-remove:hover {
    background: #dceafa;
    color: #285f96;
}

#kvc-tree-widget .kvc-meeting-managers-dropdown {
    position: fixed;

    z-index: 100120;

    display: none;

    min-width: 250px;

    padding: 6px;

    border: 1px solid #cbd5e1;
    border-radius: 7px;

    background: #ffffff;

    box-shadow:
        0 10px 30px rgba(25, 38, 58, 0.18),
        0 2px 6px rgba(25, 38, 58, 0.08);
}

#kvc-tree-widget .kvc-meeting-managers-dropdown.open {
    display: block;
}

#kvc-tree-widget .kvc-meeting-managers-search {
    width: 100%;
    height: 32px;

    padding: 0 9px;

    border: 1px solid #d4dae4;
    border-radius: 5px;

    background: #ffffff;

    color: #293448;

    font-family: inherit;
    font-size: 11px;

    outline: none;
}

#kvc-tree-widget .kvc-meeting-managers-search:focus {
    border-color: #3986ce;

    box-shadow:
        0 0 0 1px rgba(57, 134, 206, 0.10);
}

#kvc-tree-widget .kvc-meeting-managers-options {
    max-height: 190px;

    overflow-y: auto;
    overflow-x: hidden;

    margin-top: 5px;
    padding: 2px;
}

#kvc-tree-widget .kvc-meeting-manager-option {
    min-height: 30px;

    display: flex;
    align-items: center;
    gap: 8px;

    padding: 5px 7px;

    border-radius: 5px;

    color: #364154;

    font-size: 11px;
    line-height: 1.3;

    cursor: pointer;
}

#kvc-tree-widget .kvc-meeting-manager-option:hover {
    background: #f4f7fb;
}

#kvc-tree-widget .kvc-meeting-manager-checkbox {
    width: 15px;
    height: 15px;

    flex: 0 0 15px;

    margin: 0;

    cursor: pointer;
}

#kvc-tree-widget .kvc-meeting-manager-empty {
    padding: 9px 8px;

    color: #929baa;

    font-size: 11px;
}

#kvc-tree-widget .kvc-form-note {
    margin-top: 6px;

    color: #7a8493;

    font-size: 10px;
    line-height: 1.4;
}

#kvc-tree-widget .kvc-meeting-managers-options::-webkit-scrollbar {
    width: 6px;
}

#kvc-tree-widget .kvc-meeting-managers-options::-webkit-scrollbar-thumb {
    background: #d3dae4;
    border-radius: 10px;
}

/* =========================================================
   FOOTER
   ========================================================= */

#kvc-tree-widget .kvc-modal-footer {
    display: flex;

    align-items: center;
    justify-content: flex-end;

    gap: 8px;

    padding: 12px 18px;

    border-top: 1px solid #e8ebf0;

    background: #fafbfc;
}

#kvc-tree-widget .kvc-modal-button {
    height: 34px;

    padding: 0 14px;

    border-radius: 6px;

    font-family: inherit;
    font-size: 12px;
    font-weight: 600;

    cursor: pointer;
}

#kvc-tree-widget .kvc-modal-actions-left {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-right: auto;
}

#kvc-tree-widget #kvcSettingsAdd,
#kvc-tree-widget #kvcSettingsDelete {
    width: 88px;
    min-width: 88px;
    height: 34px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
}

#kvc-tree-widget .kvc-modal-button.add {
    border: 1px solid #8eb7dc;
    background: #ffffff;
    color: #2c73b6;
}

#kvc-tree-widget .kvc-modal-button.add:hover {
    background: #f3f8fd;
}

#kvc-tree-widget .kvc-modal-button.delete {
    border: 1px solid #e3a4a4;
    background: #ffffff;
    color: #c84d4d;
}

#kvc-tree-widget .kvc-modal-button.delete:hover {
    background: #fff5f5;
}

#kvc-tree-widget .kvc-modal-button.cancel {
    border: 1px solid #d4dae4;

    background: #ffffff;

    color: #465165;
}

#kvc-tree-widget .kvc-modal-button.save {
    border: 1px solid #347bc1;

    background: #347bc1;

    color: #ffffff;
}

#kvc-tree-widget .kvc-modal-button.save:hover {
    background: #286dac;
}

/* =========================================================
   СОБРАНИЯ
   ========================================================= */

#kvc-tree-widget .kvc-meeting-overlay {
    position: fixed;
    inset: 0;
    z-index: 100150;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 18px;
    background: rgba(25, 34, 49, 0.38);
}

#kvc-tree-widget .kvc-meeting-overlay.open {
    display: flex;
}

#kvc-tree-widget .kvc-meeting-modal {
    width: calc(100vw - 36px);
    max-width: 1500px;
    max-height: calc(100vh - 36px);
    display: flex;
    flex-direction: column;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(17, 27, 43, 0.28);
    overflow: hidden;
}

#kvc-tree-widget .kvc-meeting-modal.fullscreen {
    width: 100vw;
    max-width: none;
    height: 100vh;
    max-height: none;
    border-radius: 0;
}

#kvc-tree-widget .kvc-meeting-header {
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 14px 18px;
    border-bottom: 1px solid #e7ebf0;
    background: #ffffff;
}

#kvc-tree-widget .kvc-meeting-header-text {
    min-width: 0;
}

#kvc-tree-widget .kvc-meeting-header-actions {
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    gap: 6px;
}

#kvc-tree-widget .kvc-meeting-fullscreen {
    width: 32px;
    height: 32px;
    flex: 0 0 32px;
    padding: 0;
    border: 0;
    border-radius: 50%;
    background: transparent;
    color: #5579a8;
    font-family: inherit;
    font-size: 19px;
    line-height: 1;
    cursor: pointer;
}

#kvc-tree-widget .kvc-meeting-fullscreen:hover {
    background: #f2f4f7;
    color: #176fc1;
}

#kvc-tree-widget .kvc-meeting-title {
    font-size: 17px;
    line-height: 1.25;
    font-weight: 700;
    color: #263143;
}

#kvc-tree-widget .kvc-meeting-subtitle {
    margin-top: 3px;
    font-size: 11px;
    color: #7b8594;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

#kvc-tree-widget .kvc-meeting-close {
    width: 32px;
    height: 32px;
    flex: 0 0 32px;
    padding: 0;
    border: 0;
    border-radius: 50%;
    background: transparent;
    color: #687386;
    font-size: 24px;
    line-height: 1;
    cursor: pointer;
}

#kvc-tree-widget .kvc-meeting-close:hover {
    background: #f2f4f7;
}

#kvc-tree-widget .kvc-meeting-body {
    flex: 1 1 auto;
    min-height: 0;
    overflow: auto;
    padding: 16px 18px;
    background: #fafbfc;
}

#kvc-tree-widget .kvc-meeting-view {
    display: none;
}

#kvc-tree-widget .kvc-meeting-view.visible {
    display: block;
}

#kvc-tree-widget .kvc-meeting-list-toolbar {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    margin-bottom: 12px;
}

#kvc-tree-widget .kvc-meeting-create {
    height: 32px;
    padding: 0 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #347bc1;
    border-radius: 5px;
    background: #ffffff;
    color: #236da8;
    font-family: inherit;
    font-size: 11px;
    line-height: 1;
    font-weight: 600;
    white-space: nowrap;
    box-shadow: 0 1px 2px rgba(20, 30, 50, 0.04);
    cursor: pointer;
}

#kvc-tree-widget .kvc-meeting-create:hover {
    background: #f3f8fd;
    border-color: #2874b8;
    color: #185f99;
}

#kvc-tree-widget .kvc-meeting-table-box,
#kvc-tree-widget .kvc-meeting-detail-table-box {
    width: 100%;
    overflow: auto;
    background: #ffffff;
    border: 1px solid #d9dee7;
    border-radius: 8px;
}

#kvc-tree-widget .kvc-meeting-list-table {
    width: 100%;
    min-width: 900px;
    border-collapse: collapse;
    table-layout: fixed;
}

#kvc-tree-widget .kvc-meeting-list-table th,
#kvc-tree-widget .kvc-meeting-list-table td {
    border-right: 1px solid #d9dee7;
    border-bottom: 1px solid #d9dee7;
    padding: 9px 10px;
    text-align: center;
    vertical-align: middle;
    font-size: 11px;
}

#kvc-tree-widget .kvc-meeting-list-table th {
    background: #f6f7f9;
    color: #303744;
    font-weight: 700;
}

#kvc-tree-widget .kvc-meeting-list-table tr:last-child td {
    border-bottom: 0;
}

#kvc-tree-widget .kvc-meeting-list-table th:last-child,
#kvc-tree-widget .kvc-meeting-list-table td:last-child {
    border-right: 0;
}

#kvc-tree-widget .kvc-meeting-date-link {
    padding: 0;
    border: 0;
    background: transparent;
    color: #4f7698;
    font-family: inherit;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
}

#kvc-tree-widget .kvc-meeting-date-link:hover {
    color: #236aa8;
    text-decoration: underline;
}


#kvc-tree-widget .kvc-meeting-date-wrap {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    max-width: 100%;
}

#kvc-tree-widget .kvc-meeting-status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    height: 20px;
    padding: 0 7px;
    border: 1px solid transparent;
    border-radius: 10px;
    font-size: 9px;
    line-height: 1;
    font-weight: 700;
    white-space: nowrap;
}

#kvc-tree-widget .kvc-meeting-status-badge.open {
    background: #eef6ff;
    border-color: #a8c9e8;
    color: #246fae;
}

#kvc-tree-widget .kvc-meeting-status-badge.closed {
    background: #edf8f1;
    border-color: #a8d9ba;
    color: #21834d;
}

#kvc-tree-widget .kvc-meeting-list-table tr.kvc-meeting-row-open td {
    background: #f7fbff;
}

#kvc-tree-widget .kvc-meeting-list-table tr.kvc-meeting-row-open td:first-child {
    box-shadow: inset 3px 0 0 #347bc1;
}

#kvc-tree-widget .kvc-meeting-list-table tr.kvc-meeting-row-closed td {
    background: #fcfefc;
}

#kvc-tree-widget .kvc-meeting-list-table tr.kvc-meeting-row-closed td:first-child {
    box-shadow: inset 3px 0 0 #64ad7b;
}

#kvc-tree-widget .kvc-meeting-list-table tr.kvc-meeting-row-open:hover td {
    background: #f1f8ff;
}

#kvc-tree-widget .kvc-meeting-list-table tr.kvc-meeting-row-closed:hover td {
    background: #f7fcf8;
}

#kvc-tree-widget .kvc-meeting-empty {
    padding: 28px 16px !important;
    color: #8d97a6;
    font-style: italic;
}

#kvc-tree-widget .kvc-meeting-meta {
    display: grid;
    grid-template-columns: 220px minmax(320px, 560px);
    align-items: start;
    gap: 14px;
    margin-bottom: 14px;
}

#kvc-tree-widget .kvc-meeting-meta-field {
    min-width: 0;
}

#kvc-tree-widget .kvc-meeting-meta-label {
    display: block;
    margin-bottom: 5px;
    font-size: 11px;
    font-weight: 600;
    color: #687386;
}

#kvc-tree-widget .kvc-meeting-node-value,
#kvc-tree-widget .kvc-meeting-datetime {
    width: 100%;
    min-height: 36px;
    display: flex;
    align-items: center;
    padding: 7px 10px;
    border: 1px solid #d4dae4;
    border-radius: 5px;
    background: #ffffff;
    color: #293448;
    font-family: inherit;
    font-size: 12px;
}

#kvc-tree-widget .kvc-meeting-datetime {
    display: block;
    width: 220px;
    min-height: 32px;
    height: 32px;
    padding: 0 8px;
    outline: none;
}

#kvc-tree-widget .kvc-meeting-datetime::-webkit-calendar-picker-indicator {
    cursor: pointer;
    margin-left: auto;
}

#kvc-tree-widget .kvc-meeting-datetime:focus {
    border-color: #3986ce;
}

#kvc-tree-widget .kvc-meeting-datetime:disabled {
    opacity: 1;
    color: #293448;
    -webkit-text-fill-color: #293448;
    background: #f7f8fa;
    cursor: default;
}


/* =========================================================
   ВЫБОР КВЦ ПРИ СОЗДАНИИ СОБРАНИЯ
   ========================================================= */

#kvc-tree-widget .kvc-meeting-scope-field {
    position: relative;
    min-width: 0;
}

#kvc-tree-widget .kvc-meeting-scope-selection {
    position: relative;
    min-height: 32px;
    width: 100%;
    display: flex;
    align-items: center;
    padding: 3px 32px 3px 5px;
    border: 1px solid #d4dae4;
    border-radius: 5px;
    background: #ffffff;
    cursor: pointer;
}

#kvc-tree-widget .kvc-meeting-scope-selection::after {
    content: "";
    position: absolute;
    right: 11px;
    top: 50%;
    width: 6px;
    height: 6px;
    border-right: 1px solid #758092;
    border-bottom: 1px solid #758092;
    transform: translateY(-65%) rotate(45deg);
}

#kvc-tree-widget .kvc-meeting-scope-selection.open::after {
    transform: translateY(-30%) rotate(225deg);
}

#kvc-tree-widget .kvc-meeting-scope-tags {
    min-width: 0;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
}

#kvc-tree-widget .kvc-meeting-scope-tag {
    max-width: 240px;
    min-height: 23px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 2px 6px 2px 7px;
    border: 1px solid #c8d9eb;
    border-radius: 5px;
    background: #eef6ff;
    color: #2d6398;
    font-size: 10px;
    line-height: 1.25;
}

#kvc-tree-widget .kvc-meeting-scope-tag.main {
    border-color: #b7c7d8;
    background: #f3f6f9;
    color: #465b70;
}

#kvc-tree-widget .kvc-meeting-scope-tag-name {
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

#kvc-tree-widget .kvc-meeting-scope-tag-remove {
    width: 15px;
    height: 15px;
    flex: 0 0 15px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    border: 0;
    border-radius: 50%;
    background: transparent;
    color: #6683a0;
    font-size: 13px;
    line-height: 1;
    cursor: pointer;
}

#kvc-tree-widget .kvc-meeting-scope-tag-remove:hover {
    background: #dceafa;
    color: #285f96;
}

#kvc-tree-widget .kvc-meeting-scope-dropdown {
    position: absolute;
    left: 0;
    top: calc(100% + 5px);
    z-index: 100;
    display: none;
    width: 100%;
    min-width: 320px;
    padding: 6px;
    border: 1px solid #cbd5e1;
    border-radius: 7px;
    background: #ffffff;
    box-shadow: 0 10px 30px rgba(25, 38, 58, 0.18), 0 2px 6px rgba(25, 38, 58, 0.08);
}

#kvc-tree-widget .kvc-meeting-scope-dropdown.open {
    display: block;
}

#kvc-tree-widget .kvc-meeting-scope-search {
    width: 100%;
    height: 30px;
    padding: 0 8px;
    border: 1px solid #d4dae4;
    border-radius: 5px;
    background: #ffffff;
    color: #293448;
    font-family: inherit;
    font-size: 11px;
    outline: none;
}

#kvc-tree-widget .kvc-meeting-scope-options {
    max-height: 190px;
    overflow-y: auto;
    margin-top: 5px;
    padding: 2px;
}

#kvc-tree-widget .kvc-meeting-scope-option {
    min-height: 30px;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 5px 7px;
    border-radius: 5px;
    color: #364154;
    font-size: 11px;
    line-height: 1.3;
    cursor: pointer;
}

#kvc-tree-widget .kvc-meeting-scope-option:hover {
    background: #f4f7fb;
}

#kvc-tree-widget .kvc-meeting-scope-option input {
    width: 15px;
    height: 15px;
    margin: 0;
    flex: 0 0 15px;
}

#kvc-tree-widget .kvc-meeting-scope-empty {
    padding: 8px;
    color: #929baa;
    font-size: 11px;
}

#kvc-tree-widget .kvc-meeting-scope-note {
    margin-top: 5px;
    color: #7a8493;
    font-size: 10px;
    line-height: 1.35;
}

/* =========================================================
   НЕСКОЛЬКО КВЦ В ОДНОМ СОБРАНИИ
   ========================================================= */

#kvc-tree-widget .kvc-meeting-sections {
    width: 100%;
}

#kvc-tree-widget .kvc-meeting-section {
    width: 100%;
    margin-bottom: 20px;
}

#kvc-tree-widget .kvc-meeting-section:last-child {
    margin-bottom: 0;
}

#kvc-tree-widget .kvc-meeting-section-title {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0 0 7px 2px;
    color: #263143;
    font-size: 13px;
    line-height: 1.35;
    font-weight: 700;
}

#kvc-tree-widget .kvc-meeting-section-title::before {
    content: "КВЦ";
    flex: 0 0 auto;
    padding: 2px 5px;
    border-radius: 4px;
    background: #fff6e8;
    color: #ad7327;
    font-size: 8px;
    line-height: 1.1;
    font-weight: 700;
}

#kvc-tree-widget .kvc-meeting-section .kvc-meeting-detail-table-box {
    margin-bottom: 0;
}

#kvc-tree-widget .kvc-meeting-detail-table {
    width: 100%;
    min-width: 1780px;
    border-collapse: collapse;
    table-layout: fixed;
}

#kvc-tree-widget .kvc-meeting-detail-table th,
#kvc-tree-widget .kvc-meeting-detail-table td {
    border-right: 1px solid #d9dee7;
    border-bottom: 1px solid #d9dee7;
    padding: 7px 9px;
    vertical-align: middle;
    font-size: 11px;
}

#kvc-tree-widget .kvc-meeting-detail-table th {
    background: #f6f7f9;
    text-align: center;
    font-weight: 700;
}

#kvc-tree-widget .kvc-meeting-detail-table tr:last-child td {
    border-bottom: 0;
}

#kvc-tree-widget .kvc-meeting-detail-table th:last-child,
#kvc-tree-widget .kvc-meeting-detail-table td:last-child {
    border-right: 0;
}

#kvc-tree-widget .kvc-meeting-user-cell {
    width: 210px;
    min-width: 210px;
    position: sticky;
    left: 0;
    z-index: 4;
    background: #ffffff;
    color: #1768a5;
    font-weight: 600;
    border-right: 1px solid #d9dee7 !important;
    box-shadow: inset -1px 0 0 #d9dee7;
}

#kvc-tree-widget .kvc-meeting-detail-table thead .kvc-meeting-user-cell {
    z-index: 6;
    background: #f6f7f9;
    color: #303744;
}

#kvc-tree-widget .kvc-meeting-prev-obligation {
    width: 330px;
}

#kvc-tree-widget .kvc-meeting-prev-result {
    width: 220px;
}

#kvc-tree-widget .kvc-meeting-prev-status {
    width: 165px;
}

#kvc-tree-widget .kvc-meeting-prev-comment {
    width: 220px;
}

#kvc-tree-widget .kvc-meeting-current-obligation {
    width: 340px;
}

#kvc-tree-widget .kvc-meeting-current-state {
    width: 160px;
}

#kvc-tree-widget .kvc-meeting-current-comment {
    width: 220px;
}

#kvc-tree-widget .kvc-meeting-cell-input,
#kvc-tree-widget .kvc-meeting-cell-select,
#kvc-tree-widget .kvc-meeting-cell-textarea {
    width: 100%;
    border: 1px solid #d7dde6;
    border-radius: 4px;
    background: #ffffff;
    color: #273346;
    font-family: inherit;
    font-size: 11px;
    outline: none;
}

#kvc-tree-widget .kvc-meeting-cell-select,
#kvc-tree-widget .kvc-meeting-cell-input {
    height: 32px;
    padding: 0 7px;
}

#kvc-tree-widget .kvc-meeting-cell-textarea {
    min-height: 56px;
    padding: 6px 7px;
    resize: vertical;
    line-height: 1.35;
}

#kvc-tree-widget .kvc-meeting-cell-textarea[readonly] {
    border-color: transparent;
    background: transparent;
    resize: none;
}

#kvc-tree-widget .kvc-meeting-cell-select:disabled {
    opacity: 1;
    color: #273346;
    -webkit-text-fill-color: #273346;
    border-color: transparent;
    background: transparent;
    cursor: default;
}

#kvc-tree-widget .kvc-meeting-cell-textarea[readonly] {
    cursor: default;
}

#kvc-tree-widget .kvc-meeting-prev-done {
    background: #e9f9ec;
}

#kvc-tree-widget .kvc-meeting-prev-partial {
    background: #fff8df;
}

#kvc-tree-widget .kvc-meeting-prev-not-done {
    background: #fff0f0;
}

#kvc-tree-widget .kvc-meeting-prev-done .kvc-meeting-cell-textarea,
#kvc-tree-widget .kvc-meeting-prev-partial .kvc-meeting-cell-textarea,
#kvc-tree-widget .kvc-meeting-prev-not-done .kvc-meeting-cell-textarea {
    background: transparent;
}

#kvc-tree-widget .kvc-meeting-summary {
    display: grid;
    grid-template-columns: 180px minmax(0, 1fr);
    gap: 10px;
    align-items: start;
    margin-top: 18px;
}

#kvc-tree-widget .kvc-meeting-summary-label {
    padding-top: 10px;
    font-size: 12px;
    font-weight: 600;
    color: #687286;
}

#kvc-tree-widget .kvc-meeting-summary-textarea {
    width: 100%;
    min-height: 90px;
    padding: 9px 10px;
    border: 1px solid #d4dae4;
    border-radius: 5px;
    background: #ffffff;
    color: #293448;
    font-family: inherit;
    font-size: 12px;
    line-height: 1.4;
    resize: vertical;
    outline: none;
}

#kvc-tree-widget .kvc-meeting-summary-textarea:focus {
    border-color: #3986ce;
    box-shadow: 0 0 0 1px rgba(57, 134, 206, 0.10);
}

#kvc-tree-widget .kvc-meeting-summary-textarea[readonly] {
    background: #f8f9fb;
}

#kvc-tree-widget .kvc-meeting-footer {
    flex: 0 0 auto;
    display: none;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    padding: 12px 18px;
    border-top: 1px solid #e7ebf0;
    background: #ffffff;
}

#kvc-tree-widget .kvc-meeting-footer.visible {
    display: flex;
}

#kvc-tree-widget .kvc-meeting-footer-button {
    height: 34px;
    padding: 0 14px;
    border-radius: 4px;
    font-family: inherit;
    font-size: 12px;
    font-weight: 600;
    line-height: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
    cursor: pointer;
}

/* Отмена / Закрыть — белая кнопка с синей рамкой */
#kvc-tree-widget .kvc-meeting-footer-button.secondary {
    border: 1px solid #1f6f9f;
    background: #ffffff;
    color: #1f6f9f;
}

#kvc-tree-widget .kvc-meeting-footer-button.secondary:hover {
    background: #f3f8fb;
}

/* Сохранить / Редактировать — синяя кнопка */
#kvc-tree-widget .kvc-meeting-footer-button.primary {
    border: 1px solid #1f6f9f;
    background: #1f6f9f;
    color: #ffffff;
}

#kvc-tree-widget .kvc-meeting-footer-button.primary:hover {
    border-color: #185d86;
    background: #185d86;
}

/* Удалить — красная кнопка */
#kvc-tree-widget .kvc-meeting-footer-button.danger {
    border: 1px solid #d9534f;
    background: #d9534f;
    color: #ffffff;
}

#kvc-tree-widget .kvc-meeting-footer-button.danger:hover {
    border-color: #c9433f;
    background: #c9433f;
}

/* Закрыть собрание — зелёная кнопка */
#kvc-tree-widget .kvc-meeting-footer-button.success {
    border: 1px solid #2f9e44;
    background: #2f9e44;
    color: #ffffff;
}

#kvc-tree-widget .kvc-meeting-footer-button.success:hover {
    border-color: #27853a;
    background: #27853a;
}

#kvc-tree-widget .kvc-meeting-footer-button:disabled {
    opacity: 0.65;
    cursor: default;
}

/* В карточке собрания ширина зависит от текста, как у стандартных кнопок */
#kvc-tree-widget #kvcMeetingDetailFooter .kvc-meeting-footer-button {
    min-width: 0;
}

/* =========================================================
   ЛИЧНОЕ РЕДАКТИРОВАНИЕ ОБЯЗАТЕЛЬСТВ УЧАСТНИКА
   ========================================================= */

#kvc-tree-widget .kvc-meeting-user-name-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    width: 100%;
}

#kvc-tree-widget .kvc-meeting-user-name-text {
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
}

#kvc-tree-widget .kvc-meeting-user-actions {
    flex: 0 0 auto;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

#kvc-tree-widget .kvc-meeting-user-edit {
    width: 26px;
    min-width: 26px;
    height: 26px;
    flex: 0 0 26px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #b9cce0;
    border-radius: 50%;
    background: #ffffff;
    color: #3975aa;
    font-size: 13px;
    line-height: 1;
    cursor: pointer;
}

#kvc-tree-widget .kvc-meeting-user-edit:hover {
    border-color: #7fa9cf;
    background: #f3f8fd;
    color: #176fc1;
}

#kvc-tree-widget .kvc-meeting-user-edit svg {
    width: 14px;
    height: 14px;
    display: block;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
    pointer-events: none;
}

#kvc-tree-widget .kvc-meeting-user-delete {
    width: 26px;
    min-width: 26px;
    height: 26px;
    flex: 0 0 26px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e3b3b3;
    border-radius: 50%;
    background: #ffffff;
    color: #c84d4d;
    line-height: 1;
    cursor: pointer;
}

#kvc-tree-widget .kvc-meeting-user-delete:hover {
    border-color: #d87979;
    background: #fff5f5;
    color: #b73838;
}

#kvc-tree-widget .kvc-meeting-user-delete:disabled {
    opacity: 0.6;
    cursor: default;
}

#kvc-tree-widget .kvc-meeting-user-delete svg {
    width: 14px;
    height: 14px;
    display: block;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
    pointer-events: none;
}

#kvc-tree-widget .kvc-meeting-personal-overlay {
    position: fixed;
    inset: 0;
    z-index: 100250;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(25, 34, 49, 0.38);
}

#kvc-tree-widget .kvc-meeting-personal-overlay.open {
    display: flex;
}

#kvc-tree-widget .kvc-meeting-personal-modal {
    width: 540px;
    max-width: calc(100vw - 40px);
    max-height: calc(100vh - 40px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 20px 60px rgba(17, 27, 43, 0.28);
}

#kvc-tree-widget .kvc-meeting-personal-header {
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 14px 16px;
    border-bottom: 1px solid #e7ebf0;
}

#kvc-tree-widget .kvc-meeting-personal-title {
    font-size: 15px;
    line-height: 1.25;
    font-weight: 700;
    color: #263143;
}

#kvc-tree-widget .kvc-meeting-personal-subtitle {
    margin-top: 3px;
    font-size: 11px;
    color: #7b8594;
}

#kvc-tree-widget .kvc-meeting-personal-close {
    width: 30px;
    height: 30px;
    padding: 0;
    border: 0;
    border-radius: 50%;
    background: transparent;
    color: #687386;
    font-size: 22px;
    line-height: 1;
    cursor: pointer;
}

#kvc-tree-widget .kvc-meeting-personal-close:hover {
    background: #f2f4f7;
}

#kvc-tree-widget .kvc-meeting-personal-body {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
    padding: 16px;
}

#kvc-tree-widget .kvc-meeting-personal-field {
    margin-bottom: 14px;
}

#kvc-tree-widget .kvc-meeting-personal-field:last-child {
    margin-bottom: 0;
}

#kvc-tree-widget .kvc-meeting-personal-label {
    display: block;
    margin-bottom: 5px;
    font-size: 11px;
    font-weight: 600;
    color: #616b79;
}

#kvc-tree-widget .kvc-meeting-personal-textarea {
    width: 100%;
    min-height: 82px;
    padding: 9px 10px;
    border: 1px solid #d4dae4;
    border-radius: 5px;
    background: #ffffff;
    color: #293448;
    font-family: inherit;
    font-size: 12px;
    line-height: 1.4;
    resize: vertical;
    outline: none;
}

#kvc-tree-widget .kvc-meeting-personal-textarea:focus {
    border-color: #3986ce;
    box-shadow: 0 0 0 1px rgba(57, 134, 206, 0.10);
}

#kvc-tree-widget .kvc-meeting-personal-footer {
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    padding: 12px 16px;
    border-top: 1px solid #e7ebf0;
    background: #ffffff;
}

/* =========================================================
   MOBILE
   ========================================================= */

@media (max-width: 800px) {

    #kvc-tree-widget .kvc-toolbar {
        gap: 8px;
    }

    #kvc-tree-widget .kvc-canvas {
        padding: 30px 45px;
    }

    #kvc-tree-widget .kvc-branch {
        gap: 65px;
    }

    #kvc-tree-widget .kvc-card,
    #kvc-tree-widget .kvc-card.kvc {
        width: 295px;
        flex-basis: 295px;
    }

    #kvc-tree-widget .kvc-form-row {
        grid-template-columns: 1fr;
    }

    #kvc-tree-widget .kvc-meeting-meta {
        grid-template-columns: 1fr;
    }

    #kvc-tree-widget .kvc-meeting-scope-dropdown {
        min-width: 0;
    }
}
</style>


<x-catalog-page title="КВЦ">

    <div class="data-table-tools" id="kvc-catalog-tools">
        <div class="table-menu">
            <button
                class="table-menu-button"
                type="button"
                id="kvcViewMenuButton"
                aria-label="Настройки отображения"
                title="Настройки отображения"
            >
                <i data-lucide="settings-2"></i>
            </button>

            <div class="table-actions-menu" id="kvcViewMenu" hidden>
                <button
                    type="button"
                    id="kvcViewTreeVertical"
                    data-kvc-view-option
                >
                    <i data-lucide="git-branch"></i>
                    Дерево вертикальное
                    <i class="kvc-view-check" data-lucide="check"></i>
                </button>

                <button
                    type="button"
                    id="kvcViewTreeHorizontal"
                    data-kvc-view-option
                >
                    <i data-lucide="network"></i>
                    Дерево горизонтальное
                    <i class="kvc-view-check" data-lucide="check"></i>
                </button>

                <button
                    type="button"
                    id="kvcViewTable"
                    data-kvc-view-option
                >
                    <i data-lucide="table-2"></i>
                    Таблица
                    <i class="kvc-view-check" data-lucide="check"></i>
                </button>

                <div class="kvc-menu-separator" id="kvcTreeActionsSeparator"></div>

                <button
                    type="button"
                    id="kvcExpandAll"
                >
                    <i data-lucide="chevrons-down"></i>
                    Развернуть всё
                </button>

                <button
                    type="button"
                    id="kvcCollapseAll"
                >
                    <i data-lucide="chevrons-up"></i>
                    Свернуть всё
                </button>
            </div>
        </div>
    </div>

    <div id="kvc-tree-widget">

    <!-- =======================================================
         ДЕРЕВО
         ======================================================= -->

    <div
        class="kvc-view kvc-tree-view active"
        id="kvcTreeView"
    >

        <div class="kvc-scroll">

            <div
                class="kvc-canvas"
                id="kvcCanvas"
            >

                <svg
                    class="kvc-svg"
                    id="kvcConnections"
                ></svg>

                <div
                    class="kvc-tree"
                    id="kvcTree"
                ></div>

            </div>

        </div>

    </div>


    <!-- =======================================================
         ТАБЛИЦА
         ======================================================= -->

    <div
        class="kvc-view kvc-table-view"
        id="kvcTableView"
    >

        <div class="kvc-table-box">

            <table class="kvc-table">

                <thead>
                    <tr>

                        <th class="kvc-table-type">
                            Тип
                        </th>

                        <th class="kvc-table-name">
                            Описание
                        </th>

                        <th class="kvc-table-value">
                            План
                        </th>

                        <th class="kvc-table-value">
                            Факт
                        </th>

                        <th class="kvc-table-audit">
                            Автоаудит
                        </th>

                        <th class="kvc-table-owner">
                            Владелец
                        </th>

                        <th class="kvc-table-period">
                            Период
                        </th>

                        <th class="kvc-table-actions">
                            Действия
                        </th>

                    </tr>
                </thead>

                <tbody id="kvcTableBody"></tbody>

            </table>

        </div>

    </div>


    <!-- =======================================================
         НАСТРОЙКИ
         ======================================================= -->

    <div
        class="kvc-modal-overlay"
        id="kvcSettingsOverlay"
    >

        <div
            class="kvc-modal"
            id="kvcSettingsModal"
        >

            <div class="kvc-modal-header">

                <div
                    class="kvc-modal-title"
                    id="kvcSettingsTitle"
                >
                    Настройки КВЦ
                </div>

                <button
                    type="button"
                    class="kvc-modal-close"
                    id="kvcSettingsClose"
                >
                    ×
                </button>

            </div>


            <div
                class="kvc-modal-body"
                id="kvcModalBody"
            >

                <!-- =================================================
                     АДМИН
                     ================================================= -->

                <div
                    class="kvc-admin-settings"
                    id="kvcAdminSettings"
                >

                    <!-- ТИП НОВОГО ЭЛЕМЕНТА: показывается только при добавлении -->

                    <div
                        class="kvc-form-row kvc-create-type-row"
                        id="kvcCreateTypeRow"
                    >

                        <div class="kvc-form-field full">

                            <label class="kvc-form-label">
                                Тип
                            </label>

                            <select
                                class="kvc-form-select"
                                id="kvcEditNodeType"
                            >

                                <option value="kvc-op">
                                    КВЦ
                                </option>

                                <option value="op">
                                    ОП
                                </option>

                            </select>

                        </div>

                    </div>


                    <!-- ОПИСАНИЕ -->

                    <div class="kvc-form-row">

                        <div class="kvc-form-field full">

                            <label class="kvc-form-label">
                                Описание
                            </label>

                            <textarea
                                class="kvc-form-textarea"
                                id="kvcEditDescription"
                            ></textarea>

                        </div>

                    </div>


                    <!-- ДОПОЛНИТЕЛЬНО -->

                    <div class="kvc-form-row">

                        <div class="kvc-form-field full">

                            <label class="kvc-form-label">
                                Дополнительно
                            </label>

                            <textarea
                                class="kvc-form-textarea"
                                id="kvcEditAdditional"
                            ></textarea>

                        </div>

                    </div>


                    <!-- ТИП ПОКАЗАТЕЛЯ -->

                    <div class="kvc-form-row">

                        <div class="kvc-form-field full">

                            <label class="kvc-form-label">
                                Тип показателя
                            </label>

                            <select
                                class="kvc-form-select"
                                id="kvcEditIndicatorType"
                            >

                                <option value="percent">
                                    Проценты (%)
                                </option>

                                <option value="boolean">
                                    Логический (Да/Нет)
                                </option>

                                <option value="quantity">
                                    Количество (шт.)
                                </option>

                                <option value="money">
                                    Деньги (тыс. руб.)
                                </option>

                                <option value="time">
                                    Время (мин.)
                                </option>

                            </select>

                        </div>

                    </div>


                    <!-- ЛОГИЧЕСКИЙ -->

                    <div
                        class="kvc-form-row kvc-indicator-section"
                        id="kvcBooleanFields"
                    >

                        <div class="kvc-form-field">

                            <label class="kvc-form-label">
                                Да (текст)
                            </label>

                            <input
                                type="text"
                                class="kvc-form-input"
                                id="kvcEditTrueLabel"
                            >

                        </div>


                        <div class="kvc-form-field">

                            <label class="kvc-form-label">
                                Нет (текст)
                            </label>

                            <input
                                type="text"
                                class="kvc-form-input"
                                id="kvcEditFalseLabel"
                            >

                        </div>

                    </div>


                    <!-- ИСЧИСЛЯЕМЫЙ -->

                    <div
                        class="kvc-form-row kvc-indicator-section"
                        id="kvcNumericFields"
                    >

                        <div class="kvc-form-field">

                            <label
                                class="kvc-form-label"
                                id="kvcRangeFromLabel"
                            >
                                с (X)
                            </label>

                            <input
                                type="number"
                                step="any"
                                class="kvc-form-input"
                                id="kvcEditRangeFrom"
                            >

                        </div>


                        <div class="kvc-form-field">

                            <label
                                class="kvc-form-label"
                                id="kvcRangeToLabel"
                            >
                                до (Y)
                            </label>

                            <input
                                type="number"
                                step="any"
                                class="kvc-form-input"
                                id="kvcEditRangeTo"
                            >

                        </div>

                    </div>


                    <!-- ДАТЫ -->

                    <div class="kvc-form-row">

                        <div class="kvc-form-field">

                            <label class="kvc-form-label">
                                Дата начала
                            </label>

                            <input
                                type="date"
                                class="kvc-form-input"
                                id="kvcEditStartDate"
                            >

                        </div>


                        <div class="kvc-form-field">

                            <label class="kvc-form-label">
                                Дата окончания
                            </label>

                            <input
                                type="date"
                                class="kvc-form-input"
                                id="kvcEditEndDate"
                            >

                        </div>

                    </div>


                    <!-- ТЕКУЩЕЕ ЗНАЧЕНИЕ -->

                    <div class="kvc-form-row">

                        <div class="kvc-form-field">

                            <label class="kvc-form-label">
                                Дата последнего внесения значения
                            </label>

                            <input
                                type="date"
                                class="kvc-form-input"
                                id="kvcEditLastValueDate"
                            >

                        </div>


                        <div class="kvc-form-field">

                            <label class="kvc-form-label">
                                Текущее значение
                            </label>

                            <input
                                type="text"
                                class="kvc-form-input kvc-current-value-text"
                                id="kvcEditCurrentValueText"
                            >

                            <select
                                class="kvc-form-select kvc-current-value-boolean"
                                id="kvcEditCurrentValueBoolean"
                            ></select>

                        </div>

                    </div>


                    <!-- ВЛАДЕЛЕЦ -->

                    <div class="kvc-form-row">

                        <div class="kvc-form-field full">

                            <label class="kvc-form-label">
                                Владелец
                            </label>

                            <div
                                class="kvc-owner-control"
                                id="kvcOwnerControl"
                            >
                                Выберите владельца
                            </div>

                        </div>

                    </div>



                    <!-- ПРАВА НА СОБРАНИЯ -->

                    <div
                        class="kvc-form-row kvc-meeting-managers-row"
                        id="kvcMeetingManagersRow"
                    >

                        <div class="kvc-form-field full">

                            <label class="kvc-form-label">
                                Могут создавать, редактировать и удалять собрания
                            </label>

                            <div
                                class="kvc-meeting-managers-control"
                                id="kvcMeetingManagersControl"
                            >

                                <div
                                    class="kvc-meeting-managers-selection"
                                    id="kvcMeetingManagersSelection"
                                    tabindex="0"
                                >

                                    <div
                                        class="kvc-meeting-managers-tags"
                                        id="kvcMeetingManagersTags"
                                    ></div>

                                    <div
                                        class="kvc-meeting-managers-placeholder"
                                        id="kvcMeetingManagersPlaceholder"
                                    >
                                        Выберите пользователей...
                                    </div>

                                </div>

                                <div
                                    class="kvc-meeting-managers-dropdown"
                                    id="kvcMeetingManagersDropdown"
                                >

                                    <input
                                        type="text"
                                        class="kvc-meeting-managers-search"
                                        id="kvcMeetingManagersSearch"
                                        placeholder="Поиск пользователя..."
                                    >

                                    <div
                                        class="kvc-meeting-managers-options"
                                        id="kvcMeetingManagersOptions"
                                    ></div>

                                </div>

                            </div>

                            <div class="kvc-form-note">
                                Владельца КВЦ добавлять не обязательно — он может создавать, редактировать и удалять собрания автоматически.
                            </div>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     НЕ АДМИН
                     ================================================= -->

                <div
                    class="kvc-user-settings"
                    id="kvcUserSettings"
                >

                    <div class="kvc-form-row">

                        <div class="kvc-form-field full">

                            <label class="kvc-form-label">
                                Текущее значение
                            </label>


                            <div
                                class="kvc-current-text-wrap"
                                id="kvcUserCurrentTextWrap"
                            >

                                <input
                                    type="text"
                                    class="kvc-form-input"
                                    id="kvcUserCurrentValueText"
                                >

                                <span
                                    class="kvc-current-unit"
                                    id="kvcUserCurrentValueUnit"
                                ></span>

                            </div>


                            <select
                                class="kvc-form-select kvc-current-value-boolean"
                                id="kvcUserCurrentValueBoolean"
                            ></select>

                        </div>

                    </div>

                </div>

            </div>


            <div class="kvc-modal-footer">

                <div
                    class="kvc-modal-actions-left"
                    id="kvcSettingsStructureActions"
                >

                    <button
                        type="button"
                        class="kvc-modal-button add"
                        id="kvcSettingsAdd"
                    >
                        Добавить
                    </button>

                    <button
                        type="button"
                        class="kvc-modal-button delete"
                        id="kvcSettingsDelete"
                    >
                        Удалить
                    </button>

                </div>

                <button
                    type="button"
                    class="kvc-modal-button cancel"
                    id="kvcSettingsCancel"
                >
                    Отмена
                </button>

                <button
                    type="button"
                    class="kvc-modal-button save"
                    id="kvcSettingsSave"
                >
                    Сохранить
                </button>

            </div>

        </div>


        <!-- OWNER DROPDOWN -->

        <div
            class="kvc-owner-dropdown"
            id="kvcOwnerDropdown"
        >

            <div class="kvc-owner-search-wrap">

                <input
                    type="text"
                    class="kvc-owner-search"
                    id="kvcOwnerSearch"
                    placeholder="Поиск пользователя..."
                >

                <span
                    class="ti ti-search kvc-owner-search-icon"
                >
                </span>

            </div>

            <div
                class="kvc-owner-options"
                id="kvcOwnerOptions"
            ></div>

        </div>

    </div>


    <!-- =======================================================
         СОБРАНИЯ
         ======================================================= -->

    <div
        class="kvc-meeting-overlay"
        id="kvcMeetingOverlay"
    >

        <div
            class="kvc-meeting-modal"
            id="kvcMeetingModal"
        >

            <div class="kvc-meeting-header">

                <div class="kvc-meeting-header-text">

                    <div
                        class="kvc-meeting-title"
                        id="kvcMeetingTitle"
                    >
                        Собрания
                    </div>

                    <div
                        class="kvc-meeting-subtitle"
                        id="kvcMeetingSubtitle"
                    ></div>

                </div>

                <div class="kvc-meeting-header-actions">

                    <button
                        type="button"
                        class="kvc-meeting-create"
                        id="kvcMeetingCreate"
                    >
                        Создать собрание
                    </button>

                    <button
                        type="button"
                        class="kvc-meeting-fullscreen"
                        id="kvcMeetingFullscreen"
                        title="На весь экран"
                    >
                        ⛶
                    </button>

                    <button
                        type="button"
                        class="kvc-meeting-close"
                        id="kvcMeetingClose"
                    >
                        ×
                    </button>

                </div>

            </div>


            <div class="kvc-meeting-body">

                <!-- СПИСОК СОБРАНИЙ -->

                <div
                    class="kvc-meeting-view visible"
                    id="kvcMeetingsListView"
                >

                    <div class="kvc-meeting-table-box">

                        <table class="kvc-meeting-list-table">

                            <thead>

                                <tr>

                                    <th rowspan="2" style="width:190px;">
                                        Дата собрания
                                    </th>

                                    <th colspan="2">
                                        Участники собрания
                                    </th>

                                    <th colspan="3">
                                        Обязательства за прошлый период
                                    </th>

                                </tr>

                                <tr>

                                    <th>
                                        Присутствовали
                                    </th>

                                    <th>
                                        Отсутствовали
                                    </th>

                                    <th>
                                        Выполнено
                                    </th>

                                    <th>
                                        Выполнено частично
                                    </th>

                                    <th>
                                        Не выполнено
                                    </th>

                                </tr>

                            </thead>

                            <tbody id="kvcMeetingsTableBody"></tbody>

                        </table>

                    </div>

                </div>


                <!-- КАРТОЧКА СОБРАНИЯ -->

                <div
                    class="kvc-meeting-view"
                    id="kvcMeetingDetailView"
                >

                    <div
                        class="kvc-meeting-meta"
                        id="kvcMeetingMeta"
                    >

                        <div class="kvc-meeting-meta-field">

                            <label class="kvc-meeting-meta-label">
                                Дата собрания
                            </label>

                            <input
                                type="datetime-local"
                                class="kvc-meeting-datetime"
                                id="kvcMeetingDateTime"
                            >

                        </div>

                        <div
                            class="kvc-meeting-scope-field"
                            id="kvcMeetingScopeField"
                        >

                            <label class="kvc-meeting-meta-label">
                                КВЦ в собрании
                            </label>

                            <div
                                class="kvc-meeting-scope-selection"
                                id="kvcMeetingScopeSelection"
                                tabindex="0"
                            >
                                <div
                                    class="kvc-meeting-scope-tags"
                                    id="kvcMeetingScopeTags"
                                ></div>
                            </div>

                            <div
                                class="kvc-meeting-scope-dropdown"
                                id="kvcMeetingScopeDropdown"
                            >
                                <input
                                    type="text"
                                    class="kvc-meeting-scope-search"
                                    id="kvcMeetingScopeSearch"
                                    placeholder="Поиск дочернего КВЦ..."
                                >
                                <div
                                    class="kvc-meeting-scope-options"
                                    id="kvcMeetingScopeOptions"
                                ></div>
                            </div>

                            <div class="kvc-meeting-scope-note">
                                Текущий КВЦ включён всегда. Дополнительно можно выбрать дочерние КВЦ из его ветки.
                            </div>

                        </div>

                    </div>


                    <div
                        class="kvc-meeting-sections"
                        id="kvcMeetingSectionsContainer"
                    >
                    </div>
                    <div class="kvc-meeting-summary">

                        <div class="kvc-meeting-summary-label">
                            Итоги собрания
                        </div>

                        <textarea
                            class="kvc-meeting-summary-textarea"
                            id="kvcMeetingSummary"
                        ></textarea>

                    </div>

                </div>

            </div>


            <div
                class="kvc-meeting-footer visible"
                id="kvcMeetingListFooter"
            >

                <button
                    type="button"
                    class="kvc-meeting-footer-button secondary"
                    id="kvcMeetingsCloseButton"
                >
                    Закрыть
                </button>

            </div>


            <div
                class="kvc-meeting-footer"
                id="kvcMeetingDetailFooter"
            >

                <button
                    type="button"
                    class="kvc-meeting-footer-button secondary"
                    id="kvcMeetingBack"
                >
                    Отмена
                </button>

                <button
                    type="button"
                    class="kvc-meeting-footer-button primary"
                    id="kvcMeetingEdit"
                >
                    Редактировать
                </button>

                <button
                    type="button"
                    class="kvc-meeting-footer-button danger"
                    id="kvcMeetingDelete"
                >
                    Удалить
                </button>

                <button
                    type="button"
                    class="kvc-meeting-footer-button success"
                    id="kvcMeetingCloseMeeting"
                >
                    Закрыть собрание
                </button>

                <button
                    type="button"
                    class="kvc-meeting-footer-button primary"
                    id="kvcMeetingSave"
                >
                    Сохранить
                </button>

            </div>

        </div>

    </div>


    <!-- =======================================================
         ЛИЧНОЕ РЕДАКТИРОВАНИЕ ОБЯЗАТЕЛЬСТВ УЧАСТНИКА
         ======================================================= -->

    <div
        class="kvc-meeting-personal-overlay"
        id="kvcMeetingPersonalOverlay"
    >

        <div
            class="kvc-meeting-personal-modal"
            id="kvcMeetingPersonalModal"
        >

            <div class="kvc-meeting-personal-header">

                <div>
                    <div class="kvc-meeting-personal-title">
                        Мои обязательства
                    </div>

                    <div
                        class="kvc-meeting-personal-subtitle"
                        id="kvcMeetingPersonalSubtitle"
                    ></div>
                </div>

                <button
                    type="button"
                    class="kvc-meeting-personal-close"
                    id="kvcMeetingPersonalClose"
                >
                    ×
                </button>

            </div>

            <div class="kvc-meeting-personal-body">

                <div class="kvc-meeting-personal-field">
                    <label class="kvc-meeting-personal-label">
                        Обязательство
                    </label>
                    <textarea
                        class="kvc-meeting-personal-textarea"
                        id="kvcMeetingPersonalObligation"
                    ></textarea>
                </div>

                <div class="kvc-meeting-personal-field">
                    <label class="kvc-meeting-personal-label">
                        Итог
                    </label>
                    <textarea
                        class="kvc-meeting-personal-textarea"
                        id="kvcMeetingPersonalResult"
                    ></textarea>
                </div>

                <div class="kvc-meeting-personal-field">
                    <label class="kvc-meeting-personal-label">
                        Комментарий
                    </label>
                    <textarea
                        class="kvc-meeting-personal-textarea"
                        id="kvcMeetingPersonalComment"
                    ></textarea>
                </div>

            </div>

            <div class="kvc-meeting-personal-footer">

                <button
                    type="button"
                    class="kvc-meeting-footer-button secondary"
                    id="kvcMeetingPersonalCancel"
                >
                    Отмена
                </button>

                <button
                    type="button"
                    class="kvc-meeting-footer-button primary"
                    id="kvcMeetingPersonalSave"
                >
                    Сохранить
                </button>

            </div>

        </div>

    </div>

</div>


</x-catalog-page>

<script>

    /* =========================================================
       LARAVEL API
       ========================================================= */

    window.KvcApi = (function () {
        var apiBase = @json(url('/kvc/api'));
        var csrfToken = @json(csrf_token());
        var bootstrap = {
            users: @json($kvcUsers),
            currentUser: @json($kvcCurrentUser)
        };

        function encode(value) {
            return encodeURIComponent(String(value));
        }

        async function request(path, options) {
            options = options || {};
            options.credentials = "same-origin";
            options.headers = Object.assign(
                {
                    "Accept": "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest"
                },
                options.headers || {}
            );

            var response = await fetch(apiBase + path, options);
            var text = await response.text();
            var data = null;

            if (text) {
                try {
                    data = JSON.parse(text);
                } catch (error) {
                    data = text;
                }
            }

            if (!response.ok) {
                var message =
                    data && typeof data === "object" && data.message
                        ? data.message
                        : "Ошибка запроса к серверу";

                if (
                    data &&
                    typeof data === "object" &&
                    data.errors
                ) {
                    var firstErrorKey = Object.keys(data.errors)[0];
                    if (
                        firstErrorKey &&
                        Array.isArray(data.errors[firstErrorKey]) &&
                        data.errors[firstErrorKey][0]
                    ) {
                        message = data.errors[firstErrorKey][0];
                    }
                }

                var apiError = new Error(message);
                apiError.status = response.status;
                apiError.payload = data;
                throw apiError;
            }

            return data;
        }

        return {
            getKVC: function () {
                return request("/tree", { method: "GET" });
            },

            updateKVC: function (tree) {
                return request("/tree", {
                    method: "PUT",
                    body: JSON.stringify({ tree: tree })
                });
            },

            updateKVCValue: function (nodeId, value) {
                return request(
                    "/nodes/" + encode(nodeId) + "/value",
                    {
                        method: "PATCH",
                        body: JSON.stringify({ value: value })
                    }
                );
            },

            getUsers: function () {
                return Promise.resolve(bootstrap.users || []);
            },

            getCurrentUserData: function () {
                return Promise.resolve(bootstrap.currentUser || null);
            },

            getKVCMeetings: function (nodeId) {
                return request(
                    "/nodes/" + encode(nodeId) + "/meetings",
                    { method: "GET" }
                );
            },

            saveKVCMeeting: function (nodeId, meeting) {
                return request(
                    "/nodes/" + encode(nodeId) + "/meetings/save",
                    {
                        method: "POST",
                        body: JSON.stringify({ meeting: meeting })
                    }
                );
            },

            updateKVCMeetingParticipant: function (
                nodeId,
                meetingId,
                participantUserId,
                participantUserName,
                data,
                sectionNodeId
            ) {
                return request(
                    "/nodes/" + encode(nodeId) +
                    "/meetings/" + encode(meetingId) +
                    "/participant",
                    {
                        method: "PATCH",
                        body: JSON.stringify({
                            userId: participantUserId || "",
                            userName: participantUserName || "",
                            sectionNodeId: sectionNodeId || "",
                            data: data || {}
                        })
                    }
                );
            },

            deleteKVCMeetingParticipant: function (
                nodeId,
                meetingId,
                participantUserId,
                participantUserName,
                sectionNodeId
            ) {
                return request(
                    "/nodes/" + encode(nodeId) +
                    "/meetings/" + encode(meetingId) +
                    "/participant",
                    {
                        method: "DELETE",
                        body: JSON.stringify({
                            userId: participantUserId || "",
                            userName: participantUserName || "",
                            sectionNodeId: sectionNodeId || ""
                        })
                    }
                );
            },

            deleteKVCMeeting: function (nodeId, meetingId) {
                return request(
                    "/nodes/" + encode(nodeId) +
                    "/meetings/" + encode(meetingId),
                    { method: "DELETE" }
                );
            }
        };
    })();

(function () {

    /* =========================================================
       ДАННЫЕ ИЗ Laravel
       ========================================================= */

    /*
     * Здесь НЕТ тестовых данных.
     * Дерево, пользователи и текущий пользователь загружаются
     * из Laravel API ниже в loadRealData().
     */

    var allUsers = [];

    var treeData = null;

    var currentUserData = null;


    /* =========================================================
       DOM
       ========================================================= */

    var widget =
        document.getElementById(
            "kvc-tree-widget"
        );

    if (!widget) {
        return;
    }


    var catalogTools =
        document.getElementById(
            "kvc-catalog-tools"
        );

    var viewMenuButton =
        document.getElementById(
            "kvcViewMenuButton"
        );

    var viewMenu =
        document.getElementById(
            "kvcViewMenu"
        );

    var expandAllButton =
        document.getElementById(
            "kvcExpandAll"
        );

    var treeActionsSeparator =
        document.getElementById(
            "kvcTreeActionsSeparator"
        );


    function placeKvcToolsInHeading() {

        if (!catalogTools) {
            return;
        }

        var catalogPage =
            catalogTools.closest(
                ".catalog-page"
            );

        var heading =
            catalogPage
                ? catalogPage.querySelector(
                    ".catalog-heading"
                )
                : null;

        if (
            heading &&
            catalogTools.parentElement !== heading
        ) {
            heading.appendChild(
                catalogTools
            );
        }
    }


    function closeKvcViewMenu() {

        if (viewMenu) {
            viewMenu.hidden = true;
        }
    }


    placeKvcToolsInHeading();


    var viewTreeVerticalButton =
        document.getElementById("kvcViewTreeVertical");

    var viewTreeHorizontalButton =
        document.getElementById("kvcViewTreeHorizontal");

    var viewTableButton =
        document.getElementById("kvcViewTable");

    var collapseAllButton =
        document.getElementById("kvcCollapseAll");


    var treeView =
        widget.querySelector("#kvcTreeView");

    var tableView =
        widget.querySelector("#kvcTableView");


    var tree =
        widget.querySelector("#kvcTree");

    var tableBody =
        widget.querySelector("#kvcTableBody");


    var canvas =
        widget.querySelector("#kvcCanvas");

    var svg =
        widget.querySelector("#kvcConnections");


    var settingsOverlay =
        widget.querySelector("#kvcSettingsOverlay");

    var settingsModal =
        widget.querySelector("#kvcSettingsModal");

    var settingsTitle =
        widget.querySelector("#kvcSettingsTitle");

    var modalBody =
        widget.querySelector("#kvcModalBody");


    var adminSettings =
        widget.querySelector("#kvcAdminSettings");

    var userSettings =
        widget.querySelector("#kvcUserSettings");


    var createTypeRow =
        widget.querySelector("#kvcCreateTypeRow");

    var editNodeType =
        widget.querySelector("#kvcEditNodeType");


    var editDescription =
        widget.querySelector("#kvcEditDescription");

    var editAdditional =
        widget.querySelector("#kvcEditAdditional");

    var editIndicatorType =
        widget.querySelector("#kvcEditIndicatorType");


    var booleanFields =
        widget.querySelector("#kvcBooleanFields");

    var numericFields =
        widget.querySelector("#kvcNumericFields");


    var editTrueLabel =
        widget.querySelector("#kvcEditTrueLabel");

    var editFalseLabel =
        widget.querySelector("#kvcEditFalseLabel");


    var rangeFromLabel =
        widget.querySelector("#kvcRangeFromLabel");

    var rangeToLabel =
        widget.querySelector("#kvcRangeToLabel");


    var editRangeFrom =
        widget.querySelector("#kvcEditRangeFrom");

    var editRangeTo =
        widget.querySelector("#kvcEditRangeTo");


    var editStartDate =
        widget.querySelector("#kvcEditStartDate");

    var editEndDate =
        widget.querySelector("#kvcEditEndDate");

    var editLastValueDate =
        widget.querySelector("#kvcEditLastValueDate");


    var editCurrentValueText =
        widget.querySelector("#kvcEditCurrentValueText");

    var editCurrentValueBoolean =
        widget.querySelector("#kvcEditCurrentValueBoolean");


    var userCurrentTextWrap =
        widget.querySelector("#kvcUserCurrentTextWrap");

    var userCurrentValueText =
        widget.querySelector("#kvcUserCurrentValueText");

    var userCurrentValueUnit =
        widget.querySelector("#kvcUserCurrentValueUnit");

    var userCurrentValueBoolean =
        widget.querySelector("#kvcUserCurrentValueBoolean");


    var ownerControl =
        widget.querySelector("#kvcOwnerControl");

    var ownerDropdown =
        widget.querySelector("#kvcOwnerDropdown");

    var ownerSearch =
        widget.querySelector("#kvcOwnerSearch");

    var ownerOptions =
        widget.querySelector("#kvcOwnerOptions");


    var meetingManagersRow =
        widget.querySelector("#kvcMeetingManagersRow");

    var meetingManagersControl =
        widget.querySelector("#kvcMeetingManagersControl");

    var meetingManagersSelection =
        widget.querySelector("#kvcMeetingManagersSelection");

    var meetingManagersTags =
        widget.querySelector("#kvcMeetingManagersTags");

    var meetingManagersPlaceholder =
        widget.querySelector("#kvcMeetingManagersPlaceholder");

    var meetingManagersDropdown =
        widget.querySelector("#kvcMeetingManagersDropdown");

    var meetingManagersSearch =
        widget.querySelector("#kvcMeetingManagersSearch");

    var meetingManagersOptions =
        widget.querySelector("#kvcMeetingManagersOptions");


    var settingsClose =
        widget.querySelector("#kvcSettingsClose");

    var settingsCancel =
        widget.querySelector("#kvcSettingsCancel");

    var settingsStructureActions =
        widget.querySelector("#kvcSettingsStructureActions");

    var settingsAdd =
        widget.querySelector("#kvcSettingsAdd");

    var settingsDelete =
        widget.querySelector("#kvcSettingsDelete");

    var settingsSave =
        widget.querySelector("#kvcSettingsSave");



    var meetingOverlay =
        widget.querySelector("#kvcMeetingOverlay");

    var meetingModal =
        widget.querySelector("#kvcMeetingModal");

    var meetingTitle =
        widget.querySelector("#kvcMeetingTitle");

    var meetingSubtitle =
        widget.querySelector("#kvcMeetingSubtitle");

    var meetingClose =
        widget.querySelector("#kvcMeetingClose");

    var meetingFullscreen =
        widget.querySelector("#kvcMeetingFullscreen");

    var meetingsListView =
        widget.querySelector("#kvcMeetingsListView");

    var meetingDetailView =
        widget.querySelector("#kvcMeetingDetailView");

    var meetingsTableBody =
        widget.querySelector("#kvcMeetingsTableBody");

    var meetingCreate =
        widget.querySelector("#kvcMeetingCreate");

    var meetingMeta =
        widget.querySelector("#kvcMeetingMeta");

    var meetingDateTime =
        widget.querySelector("#kvcMeetingDateTime");

    var meetingSectionsContainer =
        widget.querySelector("#kvcMeetingSectionsContainer");

    var meetingScopeField =
        widget.querySelector("#kvcMeetingScopeField");

    var meetingScopeSelection =
        widget.querySelector("#kvcMeetingScopeSelection");

    var meetingScopeTags =
        widget.querySelector("#kvcMeetingScopeTags");

    var meetingScopeDropdown =
        widget.querySelector("#kvcMeetingScopeDropdown");

    var meetingScopeSearch =
        widget.querySelector("#kvcMeetingScopeSearch");

    var meetingScopeOptions =
        widget.querySelector("#kvcMeetingScopeOptions");

    var meetingSummary =
        widget.querySelector("#kvcMeetingSummary");

    var meetingListFooter =
        widget.querySelector("#kvcMeetingListFooter");

    var meetingDetailFooter =
        widget.querySelector("#kvcMeetingDetailFooter");

    var meetingsCloseButton =
        widget.querySelector("#kvcMeetingsCloseButton");

    var meetingBack =
        widget.querySelector("#kvcMeetingBack");

    var meetingDelete =
        widget.querySelector("#kvcMeetingDelete");

    var meetingEdit =
        widget.querySelector("#kvcMeetingEdit");

    var meetingCloseMeeting =
        widget.querySelector("#kvcMeetingCloseMeeting");

    var meetingSave =
        widget.querySelector("#kvcMeetingSave");


    var meetingPersonalOverlay =
        widget.querySelector("#kvcMeetingPersonalOverlay");

    var meetingPersonalModal =
        widget.querySelector("#kvcMeetingPersonalModal");

    var meetingPersonalSubtitle =
        widget.querySelector("#kvcMeetingPersonalSubtitle");

    var meetingPersonalClose =
        widget.querySelector("#kvcMeetingPersonalClose");

    var meetingPersonalCancel =
        widget.querySelector("#kvcMeetingPersonalCancel");

    var meetingPersonalSave =
        widget.querySelector("#kvcMeetingPersonalSave");

    var meetingPersonalObligation =
        widget.querySelector("#kvcMeetingPersonalObligation");

    var meetingPersonalResult =
        widget.querySelector("#kvcMeetingPersonalResult");

    var meetingPersonalComment =
        widget.querySelector("#kvcMeetingPersonalComment");


    /* =========================================================
       STATE
       ========================================================= */

    /* Реальный статус администратора приходит из Laravel. */
    var isAdmin = false;

    var currentView = "tree-horizontal";

    var collapsed = {};

    var currentSettingsNode = null;

    var isCreatingNode = false;

    var creatingParentNode = null;

    var selectedOwnerId = null;

    var selectedOwnerName = "";

    var selectedMeetingManagerIds = [];


    var draggedNodeId = null;

    var draggedParentId = null;

    var draggedSourceCard = null;

    var draggedTargetNodeId = null;

    var draggedTargetAfter = false;

    var draggedPointerId = null;



    var currentMeetingsNode = null;

    var currentMeetings = [];

    var currentMeeting = null;

    var currentMeetingIsNew = false;

    var currentMeetingIsEditing = false;

    var currentUserCanManageMeetings = false;

    /*
     * Снимок личных полей участников на момент входа
     * в режим редактирования всего собрания.
     * Нужен, чтобы сохранять в отдельные записи участника
     * только действительно изменённые поля и не затирать
     * параллельные изменения других пользователей.
     */
    var currentMeetingEditParticipantSnapshot = null;

    var currentPersonalMeetingUserId = null;

    var currentPersonalMeetingUserName = "";

    var currentPersonalMeetingSectionNodeId = "";


    /* =========================================================
       ВОССТАНОВЛЕНИЕ СОСТОЯНИЯ ПОСЛЕ ПЕРЕРИСОВКИ Laravel

       Состояние интерфейса сохраняется в sessionStorage текущей
       вкладки, чтобы после обычного обновления страницы пользователь
       вернулся к тому же виду, прокрутке и незавершённой форме.

       В sessionStorage попадает только UI/черновик:
       - выбранный вид;
       - раскрытые ветки;
       - позиции прокрутки;
       - открытые окна;
       - незавершённые значения форм;
       - незавершённое собрание / редактирование обязательств.

       Реальные сохранённые данные КВЦ по-прежнему находятся
       в базе данных и загружаются через KvcApi.
       ========================================================= */

    var KVC_UI_STATE_VERSION = 3;

    var KVC_UI_STATE_MAX_AGE_MS =
        24 * 60 * 60 * 1000;

    var KVC_UI_STATE_KEY =
        "kvc_ui_state_v3::" +
        String(
            window.location &&
            window.location.pathname
                ? window.location.pathname
                : "kvc"
        );

    var KVC_UI_MANAGER_KEY =
        "__KVC_UI_MANAGER_V3__";

    var uiStateRestoring = false;

    var uiStateSaveTimer = null;

    var pendingUiState = null;


    function cloneUiValue(value) {

        if (
            value === null ||
            value === undefined
        ) {
            return value;
        }

        try {
            return JSON.parse(
                JSON.stringify(value)
            );
        } catch (error) {
            return null;
        }
    }


    function readKvcUiState() {

        try {

            var raw =
                window.sessionStorage
                    .getItem(
                        KVC_UI_STATE_KEY
                    );

            if (!raw) {
                return null;
            }

            var state =
                JSON.parse(raw);

            if (
                !state ||
                Number(state.version) !==
                    KVC_UI_STATE_VERSION
            ) {
                return null;
            }

            if (
                state.savedAt &&
                Date.now() - Number(state.savedAt) >
                    KVC_UI_STATE_MAX_AGE_MS
            ) {

                window.sessionStorage
                    .removeItem(
                        KVC_UI_STATE_KEY
                    );

                return null;
            }

            return state;

        } catch (error) {

            console.warn(
                "[KVC STATE] Не удалось прочитать состояние интерфейса",
                error
            );

            return null;
        }
    }


    function collectKvcFormValues() {

        var result = {};

        try {

            var controls =
                widget.querySelectorAll(
                    "input[id], textarea[id], select[id]"
                );

            for (
                var i = 0;
                i < controls.length;
                i++
            ) {

                var control =
                    controls[i];

                if (!control.id) {
                    continue;
                }

                if (
                    control.type === "checkbox" ||
                    control.type === "radio"
                ) {

                    result[control.id] = {
                        checked: !!control.checked
                    };

                } else {

                    result[control.id] = {
                        value:
                            control.value !== undefined
                                ? String(control.value)
                                : ""
                    };
                }
            }

        } catch (error) {
        }

        return result;
    }


    function restoreKvcFormValues(values) {

        if (!values) {
            return;
        }

        try {

            Object.keys(values)
                .forEach(
                    function (id) {

                        var control =
                            widget.querySelector(
                                "#" + id
                            );

                        if (!control) {
                            return;
                        }

                        var saved =
                            values[id] || {};

                        if (
                            control.type === "checkbox" ||
                            control.type === "radio"
                        ) {

                            if (
                                saved.checked !== undefined
                            ) {
                                control.checked =
                                    !!saved.checked;
                            }

                        } else if (
                            saved.value !== undefined
                        ) {

                            control.value =
                                saved.value;
                        }
                    }
                );

        } catch (error) {

            console.warn(
                "[KVC STATE] Не удалось восстановить значения формы",
                error
            );
        }
    }


    function collectKvcScrollState() {

        var result = {};

        try {

            var elements =
                widget.querySelectorAll(
                    "[id]"
                );

            for (
                var i = 0;
                i < elements.length;
                i++
            ) {

                var element =
                    elements[i];

                if (
                    !element.id ||
                    (
                        !element.scrollTop &&
                        !element.scrollLeft
                    )
                ) {
                    continue;
                }

                result[element.id] = {
                    top:
                        Number(
                            element.scrollTop || 0
                        ),

                    left:
                        Number(
                            element.scrollLeft || 0
                        )
                };
            }

            var mainScroll =
                widget.querySelector(
                    ".kvc-scroll"
                );

            if (mainScroll) {

                result.__mainTreeScroll = {
                    top:
                        Number(
                            mainScroll.scrollTop || 0
                        ),

                    left:
                        Number(
                            mainScroll.scrollLeft || 0
                        )
                };
            }

        } catch (error) {
        }

        return result;
    }


    function restoreKvcScrollState(scrollState) {

        if (!scrollState) {
            return;
        }

        try {

            Object.keys(scrollState)
                .forEach(
                    function (id) {

                        if (
                            id === "__mainTreeScroll"
                        ) {
                            return;
                        }

                        var element =
                            widget.querySelector(
                                "#" + id
                            );

                        if (!element) {
                            return;
                        }

                        var saved =
                            scrollState[id] || {};

                        element.scrollTop =
                            Number(saved.top || 0);

                        element.scrollLeft =
                            Number(saved.left || 0);
                    }
                );

            var mainSaved =
                scrollState.__mainTreeScroll;

            var mainScroll =
                widget.querySelector(
                    ".kvc-scroll"
                );

            if (
                mainSaved &&
                mainScroll
            ) {

                mainScroll.scrollTop =
                    Number(
                        mainSaved.top || 0
                    );

                mainScroll.scrollLeft =
                    Number(
                        mainSaved.left || 0
                    );
            }

        } catch (error) {
        }
    }


    function collectKvcFocusState() {

        try {

            var active =
                document.activeElement;

            if (
                !active ||
                !active.id ||
                !widget.contains(active)
            ) {
                return null;
            }

            var result = {
                id: active.id
            };

            if (
                typeof active.selectionStart ===
                    "number"
            ) {
                result.selectionStart =
                    active.selectionStart;
            }

            if (
                typeof active.selectionEnd ===
                    "number"
            ) {
                result.selectionEnd =
                    active.selectionEnd;
            }

            return result;

        } catch (error) {
            return null;
        }
    }


    function restoreKvcFocusState(focusState) {

        if (
            !focusState ||
            !focusState.id
        ) {
            return;
        }

        try {

            var element =
                widget.querySelector(
                    "#" + focusState.id
                );

            if (!element) {
                return;
            }

            element.focus();

            if (
                typeof element.setSelectionRange ===
                    "function" &&
                focusState.selectionStart !== undefined &&
                focusState.selectionEnd !== undefined
            ) {

                element.setSelectionRange(
                    Number(
                        focusState.selectionStart
                    ),
                    Number(
                        focusState.selectionEnd
                    )
                );
            }

        } catch (error) {
        }
    }


    function buildKvcUiState() {

        var settingsOpen =
            !!(
                settingsOverlay &&
                settingsOverlay.classList.contains(
                    "open"
                )
            );

        var meetingsOpen =
            !!(
                meetingOverlay &&
                meetingOverlay.classList.contains(
                    "open"
                )
            );

        var personalOpen =
            !!(
                meetingPersonalOverlay &&
                meetingPersonalOverlay.classList.contains(
                    "open"
                )
            );

        return {
            version:
                KVC_UI_STATE_VERSION,

            savedAt:
                Date.now(),

            currentView:
                currentView,

            collapsed:
                cloneUiValue(
                    collapsed
                ) || {},

            formValues:
                collectKvcFormValues(),

            scrollState:
                collectKvcScrollState(),

            focusState:
                collectKvcFocusState(),

            settingsOpen:
                settingsOpen,

            settingsNodeId:
                currentSettingsNode &&
                currentSettingsNode.id !== undefined
                    ? String(
                        currentSettingsNode.id
                    )
                    : null,

            settingsCreateMode:
                !!isCreatingNode,

            settingsNodeDraft:
                settingsOpen &&
                isCreatingNode
                    ? cloneUiValue(
                        currentSettingsNode
                    )
                    : null,

            creatingParentNodeId:
                creatingParentNode &&
                creatingParentNode.id !== undefined
                    ? String(
                        creatingParentNode.id
                    )
                    : null,

            selectedOwnerId:
                selectedOwnerId,

            selectedOwnerName:
                selectedOwnerName,

            selectedMeetingManagerIds:
                cloneUiValue(
                    selectedMeetingManagerIds
                ) || [],

            meetingsOpen:
                meetingsOpen,

            meetingsNodeId:
                currentMeetingsNode &&
                currentMeetingsNode.id !== undefined
                    ? String(
                        currentMeetingsNode.id
                    )
                    : null,

            meetingDetailOpen:
                !!(
                    meetingDetailView &&
                    meetingDetailView.classList.contains(
                        "visible"
                    )
                ),

            currentMeetingId:
                currentMeeting &&
                currentMeeting.id !== undefined
                    ? String(
                        currentMeeting.id
                    )
                    : null,

            currentMeetingIsNew:
                !!currentMeetingIsNew,

            currentMeetingIsEditing:
                !!currentMeetingIsEditing,

            currentMeetingDraft:
                meetingsOpen &&
                currentMeeting
                    ? cloneUiValue(
                        currentMeeting
                    )
                    : null,

            currentMeetingEditParticipantSnapshot:
                cloneUiValue(
                    currentMeetingEditParticipantSnapshot
                ),

            meetingFullscreen:
                !!(
                    meetingModal &&
                    meetingModal.classList.contains(
                        "fullscreen"
                    )
                ),

            personalOpen:
                personalOpen,

            personalUserId:
                currentPersonalMeetingUserId,

            personalUserName:
                currentPersonalMeetingUserName,

            personalSectionNodeId:
                currentPersonalMeetingSectionNodeId,

            personalDraft:
                personalOpen
                    ? {
                        obligation:
                            meetingPersonalObligation.value || "",

                        result:
                            meetingPersonalResult.value || "",

                        comment:
                            meetingPersonalComment.value || ""
                    }
                    : null
        };
    }


    function saveKvcUiStateNow() {

        if (
            uiStateRestoring
        ) {
            return;
        }

        try {

            var state =
                buildKvcUiState();

            window.sessionStorage
                .setItem(
                    KVC_UI_STATE_KEY,
                    JSON.stringify(state)
                );

        } catch (error) {

            console.warn(
                "[KVC STATE] Не удалось сохранить состояние интерфейса",
                error
            );
        }
    }


    function scheduleKvcUiStateSave(delay) {

        if (
            uiStateRestoring
        ) {
            return;
        }

        if (
            uiStateSaveTimer
        ) {
            clearTimeout(
                uiStateSaveTimer
            );
        }

        uiStateSaveTimer =
            setTimeout(
                function () {

                    uiStateSaveTimer = null;

                    saveKvcUiStateNow();
                },
                delay === undefined
                    ? 40
                    : delay
            );
    }


    function applyPendingBaseUiState() {

        if (!pendingUiState) {
            return false;
        }

        if (
            pendingUiState.currentView === "tree-vertical" ||
            pendingUiState.currentView === "tree-horizontal" ||
            pendingUiState.currentView === "table"
        ) {

            currentView =
                pendingUiState.currentView;
        }

        if (
            pendingUiState.collapsed &&
            typeof pendingUiState.collapsed ===
                "object"
        ) {

            collapsed =
                cloneUiValue(
                    pendingUiState.collapsed
                ) || {};

            return true;
        }

        return false;
    }

    function findMeetingParticipantForRestore(
        meeting,
        userId,
        userName,
        sectionNodeId
    ) {

        normalizeMeetingSectionsLocal(
            meeting
        );

        var participants =
            meeting &&
            Array.isArray(
                meeting.participants
            )
                ? meeting.participants
                : [];

        var targetId =
            userId !== null &&
            userId !== undefined
                ? String(userId)
                : "";

        var targetName =
            userName !== null &&
            userName !== undefined
                ? String(userName)
                : "";

        var targetSectionId =
            sectionNodeId !== null &&
            sectionNodeId !== undefined &&
            String(sectionNodeId)
                ? String(sectionNodeId)
                : getMeetingMainSectionNodeIdLocal(
                    meeting
                );

        for (
            var i = 0;
            i < participants.length;
            i++
        ) {

            var participant =
                participants[i];

            if (!participant) {
                continue;
            }

            if (
                String(
                    getParticipantMeetingSectionIdLocal(
                        participant,
                        meeting
                    )
                ) !== targetSectionId
            ) {
                continue;
            }

            if (
                targetId &&
                String(
                    participant.userId || ""
                ) === targetId
            ) {
                return participant;
            }

            if (
                targetName &&
                String(
                    participant.userName || ""
                ) === targetName
            ) {
                return participant;
            }
        }

        return null;
    }



    async function restoreKvcUiStateAfterLoad() {

        var state =
            pendingUiState;

        if (!state) {
            saveKvcUiStateNow();
            return;
        }

        uiStateRestoring =
            true;

        try {

            /* =============================================
               НАСТРОЙКИ / ТЕКУЩЕЕ ЗНАЧЕНИЕ
               ============================================= */

            if (
                state.settingsOpen
            ) {

                var settingsNode =
                    null;

                if (
                    state.settingsCreateMode &&
                    state.settingsNodeDraft
                ) {

                    settingsNode =
                        cloneUiValue(
                            state.settingsNodeDraft
                        );

                    creatingParentNode =
                        findNodeByIdLocal(
                            treeData,
                            state.creatingParentNodeId
                        );

                    isCreatingNode =
                        true;

                    if (
                        settingsNode &&
                        creatingParentNode
                    ) {

                        openSettings(
                            settingsNode,
                            true
                        );
                    }

                } else {

                    settingsNode =
                        findNodeByIdLocal(
                            treeData,
                            state.settingsNodeId
                        );

                    if (settingsNode) {

                        isCreatingNode =
                            false;

                        creatingParentNode =
                            null;

                        openSettings(
                            settingsNode,
                            false
                        );
                    }
                }

                if (
                    settingsOverlay.classList.contains(
                        "open"
                    )
                ) {

                    selectedOwnerId =
                        state.selectedOwnerId;

                    selectedOwnerName =
                        state.selectedOwnerName || "";

                    selectedMeetingManagerIds =
                        Array.isArray(
                            state.selectedMeetingManagerIds
                        )
                            ? state.selectedMeetingManagerIds.slice()
                            : [];

                    setOwnerControl(
                        selectedOwnerName
                    );

                    renderMeetingManagerTags();

                    renderMeetingManagerOptions(
                        meetingManagersSearch.value || ""
                    );

                    restoreKvcFormValues(
                        state.formValues
                    );

                    updateIndicatorFields();
                }
            }


            /* =============================================
               СОБРАНИЯ
               ============================================= */

            if (
                state.meetingsOpen &&
                state.meetingsNodeId
            ) {

                var meetingsNode =
                    findNodeByIdLocal(
                        treeData,
                        state.meetingsNodeId
                    );

                if (meetingsNode) {

                    await openMeetings(
                        meetingsNode
                    );

                    setMeetingFullscreen(
                        !!state.meetingFullscreen
                    );

                    if (
                        state.meetingDetailOpen &&
                        state.currentMeetingDraft
                    ) {

                        var meetingToOpen =
                            cloneUiValue(
                                state.currentMeetingDraft
                            );

                        if (
                            !state.currentMeetingIsNew &&
                            state.currentMeetingId
                        ) {

                            var freshMeeting =
                                null;

                            for (
                                var meetingIndex = 0;
                                meetingIndex < currentMeetings.length;
                                meetingIndex++
                            ) {

                                if (
                                    String(
                                        currentMeetings[meetingIndex].id
                                    ) ===
                                    String(
                                        state.currentMeetingId
                                    )
                                ) {

                                    freshMeeting =
                                        currentMeetings[meetingIndex];

                                    break;
                                }
                            }

                            if (
                                freshMeeting &&
                                !state.currentMeetingIsEditing
                            ) {

                                meetingToOpen =
                                    cloneUiValue(
                                        freshMeeting
                                    );

                            } else if (
                                freshMeeting &&
                                freshMeeting.closed
                            ) {

                                meetingToOpen.closed =
                                    true;
                            }
                        }

                        if (meetingToOpen) {

                            openMeetingDetail(
                                meetingToOpen,
                                !!state.currentMeetingIsNew
                            );

                            if (
                                !state.currentMeetingIsNew &&
                                state.currentMeetingIsEditing &&
                                !currentMeeting.closed
                            ) {

                                currentMeetingIsEditing =
                                    true;

                                currentMeetingEditParticipantSnapshot =
                                    cloneUiValue(
                                        state.currentMeetingEditParticipantSnapshot
                                    ) ||
                                    createMeetingParticipantEditSnapshot(
                                        currentMeeting
                                    );

                                updateMeetingDetailMode();
                            }

                            restoreKvcFormValues(
                                state.formValues
                            );

                            if (
                                state.personalOpen &&
                                state.personalDraft
                            ) {

                                var personalParticipant =
                                    findMeetingParticipantForRestore(
                                        currentMeeting,
                                        state.personalUserId,
                                        state.personalUserName,
                                        state.personalSectionNodeId
                                    );

                                if (personalParticipant) {

                                    openPersonalMeetingEdit(
                                        personalParticipant
                                    );

                                    if (
                                        meetingPersonalOverlay
                                            .classList
                                            .contains("open")
                                    ) {

                                        meetingPersonalObligation.value =
                                            state.personalDraft.obligation || "";

                                        meetingPersonalResult.value =
                                            state.personalDraft.result || "";

                                        meetingPersonalComment.value =
                                            state.personalDraft.comment || "";
                                    }
                                }
                            }
                        }
                    }
                }
            }


            /* =============================================
               ФОРМЫ / ПРОКРУТКА / ФОКУС
               ============================================= */

            restoreKvcFormValues(
                state.formValues
            );

            setTimeout(
                function () {

                    restoreKvcScrollState(
                        state.scrollState
                    );

                    setTimeout(
                        function () {

                            restoreKvcScrollState(
                                state.scrollState
                            );

                            restoreKvcFocusState(
                                state.focusState
                            );

                        },
                        120
                    );

                },
                0
            );

            console.log(
                "[KVC STATE] Состояние интерфейса восстановлено"
            );

        } catch (error) {

            console.error(
                "[KVC STATE] Ошибка восстановления состояния",
                error
            );

        } finally {

            uiStateRestoring =
                false;

            pendingUiState =
                null;

            setTimeout(
                function () {
                    saveKvcUiStateNow();
                },
                250
            );
        }
    }


    function installKvcUiStateTracking() {

        /*
         * При повторном открытии страницы в той же вкладке
         * останавливаем таймер предыдущего экземпляра.
         */
        try {

            var previousManager =
                window[KVC_UI_MANAGER_KEY];

            if (
                previousManager &&
                previousManager.intervalId
            ) {

                clearInterval(
                    previousManager.intervalId
                );
            }

            if (
                previousManager &&
                previousManager.observer
            ) {

                previousManager.observer.disconnect();
            }

        } catch (error) {
        }


        var manager = {
            intervalId:
                null,

            observer:
                null
        };

        window[KVC_UI_MANAGER_KEY] =
            manager;


        widget.addEventListener(
            "input",
            function () {
                scheduleKvcUiStateSave(20);
            },
            true
        );

        widget.addEventListener(
            "change",
            function () {
                scheduleKvcUiStateSave(20);
            },
            true
        );

        widget.addEventListener(
            "click",
            function () {
                scheduleKvcUiStateSave(60);
            },
            true
        );

        widget.addEventListener(
            "scroll",
            function () {
                scheduleKvcUiStateSave(80);
            },
            true
        );


        window.addEventListener(
            "pagehide",
            saveKvcUiStateNow
        );

        window.addEventListener(
            "beforeunload",
            saveKvcUiStateNow
        );


        manager.intervalId =
            setInterval(
                function () {

                    saveKvcUiStateNow();
                },
                500
            );


        try {

            manager.observer =
                new MutationObserver(
                    function () {

                        if (
                            document.body &&
                            !document.body.contains(widget)
                        ) {

                            /*
                             * DOM уже отсоединён, но ссылка widget и
                             * переменные замыкания ещё живы — последний
                             * снимок можно сохранить перед новым запуском.
                             */
                            saveKvcUiStateNow();

                            if (
                                manager.intervalId
                            ) {
                                clearInterval(
                                    manager.intervalId
                                );
                            }

                            manager.observer.disconnect();
                        }
                    }
                );

            manager.observer.observe(
                document.body,
                {
                    childList: true,
                    subtree: true
                }
            );

        } catch (error) {
        }
    }


    pendingUiState =
        readKvcUiState();

    installKvcUiStateTracking();


    /* =========================================================
       COLLAPSE
       ========================================================= */

    function collapseAllByDefault(node) {

        if (
            node.children &&
            node.children.length
        ) {

            collapsed[node.id] = true;

            for (
                var i = 0;
                i < node.children.length;
                i++
            ) {

                collapseAllByDefault(
                    node.children[i]
                );
            }
        }
    }




    /* =========================================================
       HELPERS
       ========================================================= */

    function escapeHtml(value) {

        if (
            value === null ||
            value === undefined
        ) {
            return "";
        }

        return String(value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }


    function createSvgElement(name) {

        return document.createElementNS(
            "http://www.w3.org/2000/svg",
            name
        );
    }


    function formatDate(value) {

        if (!value) {
            return "—";
        }

        var parts =
            value.split("-");

        if (
            parts.length !== 3
        ) {
            return value;
        }

        return (
            parts[2] +
            "." +
            parts[1] +
            "." +
            parts[0]
        );
    }


    /* =========================================================
       ЕДИНИЦЫ
       ========================================================= */

    function getIndicatorUnit(type) {

        if (type === "percent") {
            return "%";
        }

        if (type === "quantity") {
            return "шт.";
        }

        if (type === "money") {
            return "тыс. руб.";
        }

        if (type === "time") {
            return "мин.";
        }

        return "";
    }


    function isNumericIndicator(type) {

        return (
            type === "percent" ||
            type === "quantity" ||
            type === "money" ||
            type === "time"
        );
    }


    /* =========================================================
       РАСЧЁТ ПЛАНА И ФАКТА

       Числовой показатель:
       - План рассчитывается по траектории от rangeFrom к rangeTo
         между startDate и endDate;
       - до начала = rangeFrom;
       - после окончания = rangeTo;
       - внутри периода = линейное значение;
       - план округляется до целого;
       - факт берётся из currentValue.

       Логический показатель:
       - План = положительное значение (trueLabel);
       - Факт = currentValue.

       Старые node.plan / node.fact используются только как fallback,
       если в старых данных не хватает полей для расчёта.
       ========================================================= */

    function parseNumericValue(value) {

        if (
            value === null ||
            value === undefined ||
            value === ""
        ) {
            return null;
        }

        if (
            typeof value === "number"
        ) {
            return isFinite(value)
                ? value
                : null;
        }

        var text =
            String(value)
                .replace(/\s+/g, "")
                .replace(",", ".");

        var match =
            text.match(/-?\d+(?:\.\d+)?/);

        if (!match) {
            return null;
        }

        var number =
            Number(match[0]);

        return isFinite(number)
            ? number
            : null;
    }


    function parseLocalDate(value) {

        if (!value) {
            return null;
        }

        var parts =
            String(value).split("-");

        if (parts.length !== 3) {
            return null;
        }

        var year = Number(parts[0]);
        var month = Number(parts[1]);
        var day = Number(parts[2]);

        if (
            !year ||
            !month ||
            !day
        ) {
            return null;
        }

        return new Date(
            year,
            month - 1,
            day,
            12,
            0,
            0,
            0
        );
    }


    function calculatePlanValue(node) {

        if (
            !node
        ) {
            return null;
        }

        if (
            node.indicatorType === "boolean"
        ) {
            return true;
        }

        var from =
            parseNumericValue(
                node.rangeFrom
            );

        var to =
            parseNumericValue(
                node.rangeTo
            );

        /*
         * Совместимость со старыми записями.
         */
        if (
            from === null ||
            to === null
        ) {

            var legacyPlan =
                parseNumericValue(
                    node.plan
                );

            return legacyPlan !== null
                ? Math.round(legacyPlan)
                : null;
        }

        var start =
            parseLocalDate(
                node.startDate
            );

        var end =
            parseLocalDate(
                node.endDate
            );

        /*
         * Если период не задан — планом считается конечная цель.
         */
        if (
            !start ||
            !end ||
            end.getTime() <= start.getTime()
        ) {
            return Math.round(to);
        }

        var now =
            new Date();

        now = new Date(
            now.getFullYear(),
            now.getMonth(),
            now.getDate(),
            12,
            0,
            0,
            0
        );

        var currentTime =
            now.getTime();

        var startTime =
            start.getTime();

        var endTime =
            end.getTime();

        if (
            currentTime <= startTime
        ) {
            return Math.round(from);
        }

        if (
            currentTime >= endTime
        ) {
            return Math.round(to);
        }

        var progress =
            (
                currentTime -
                startTime
            ) /
            (
                endTime -
                startTime
            );

        var plan =
            from +
            (
                to -
                from
            ) *
            progress;

        return Math.round(plan);
    }


    function normalizeBooleanValue(
        node,
        value
    ) {

        if (
            value === true ||
            value === 1 ||
            value === "1" ||
            value === "true"
        ) {
            return true;
        }

        if (
            value === false ||
            value === 0 ||
            value === "0" ||
            value === "false"
        ) {
            return false;
        }

        var text =
            value === null ||
            value === undefined
                ? ""
                : String(value).trim();

        if (!text) {
            return null;
        }

        if (
            text ===
            String(
                node.trueLabel || "Да"
            )
        ) {
            return true;
        }

        if (
            text ===
            String(
                node.falseLabel || "Нет"
            )
        ) {
            return false;
        }

        return null;
    }


    function calculateFactValue(node) {

        if (!node) {
            return null;
        }

        if (
            node.indicatorType === "boolean"
        ) {

            var booleanFact =
                normalizeBooleanValue(
                    node,
                    node.currentValue
                );

            if (
                booleanFact !== null
            ) {
                return booleanFact;
            }

            return normalizeBooleanValue(
                node,
                node.fact
            );
        }

        var currentFact =
            parseNumericValue(
                node.currentValue
            );

        if (
            currentFact !== null
        ) {
            return currentFact;
        }

        return parseNumericValue(
            node.fact
        );
    }


    function getCalculatedMetrics(node) {

        return {
            plan: calculatePlanValue(node),
            fact: calculateFactValue(node)
        };
    }


    function getFactStatus(
        node,
        planValue,
        factValue
    ) {

        if (
            planValue === null ||
            planValue === undefined ||
            factValue === null ||
            factValue === undefined
        ) {
            return "";
        }

        if (
            node.indicatorType === "boolean"
        ) {
            return planValue === factValue
                ? "good"
                : "bad";
        }

        var from =
            parseNumericValue(
                node.rangeFrom
            );

        var to =
            parseNumericValue(
                node.rangeTo
            );

        /*
         * Если цель уменьшается (например время), меньше плана лучше.
         * В остальных случаях факт >= план — зелёный.
         */
        if (
            from !== null &&
            to !== null &&
            to < from
        ) {
            return factValue <= planValue
                ? "good"
                : "bad";
        }

        return factValue >= planValue
            ? "good"
            : "bad";
    }


    function formatIndicatorValueHtml(
        node,
        value
    ) {

        if (
            value === null ||
            value === undefined ||
            value === ""
        ) {
            return "—";
        }

        if (
            node.indicatorType === "boolean"
        ) {
            return escapeHtml(
                formatIndicatorValue(
                    node,
                    value
                )
            );
        }

        var unit =
            getIndicatorUnit(
                node.indicatorType
            );

        var numberText =
            String(value)
                .replace(".", ",");

        return (
            '<span class="kvc-value-number">' +
                escapeHtml(numberText) +
            '</span>' +
            (
                unit
                    ? '<span class="kvc-value-unit">' +
                        escapeHtml(unit) +
                      '</span>'
                    : ""
            )
        );
    }


    function formatIndicatorValue(
        node,
        value
    ) {

        if (
            value === null ||
            value === undefined ||
            value === ""
        ) {
            return "—";
        }


        if (
            node.indicatorType ===
            "boolean"
        ) {

            var trueLabel =
                node.trueLabel || "Да";

            var falseLabel =
                node.falseLabel || "Нет";


            if (
                value === true ||
                value === 1 ||
                value === "1" ||
                value === "true"
            ) {
                return trueLabel;
            }


            if (
                value === false ||
                value === 0 ||
                value === "0" ||
                value === "false"
            ) {
                return falseLabel;
            }


            return String(value);
        }


        var unit =
            getIndicatorUnit(
                node.indicatorType
            );


        var stringValue =
            String(value);


        if (
            node.indicatorType ===
            "percent"
        ) {

            if (
                stringValue.indexOf("%") !== -1
            ) {
                return stringValue;
            }

            return stringValue + "%";
        }


        if (unit) {

            if (
                stringValue
                    .toLowerCase()
                    .indexOf(
                        unit.toLowerCase()
                    ) !== -1
            ) {
                return stringValue;
            }

            return (
                stringValue +
                " " +
                unit
            );
        }


        return stringValue;
    }


    function isLongIndicatorValue(
        node,
        value
    ) {

        return (
            formatIndicatorValue(
                node,
                value
            ).length > 10
        );
    }


    /* =========================================================
       РОЛЬ

       Режим больше не переключается вручную.
       isAdmin заполняется из getCurrentUserData().isAdmin,
       который определяется в TypeScript по системной группе
       Laravel «Администраторы».
       ========================================================= */


    /* =========================================================
       ВИД
       ========================================================= */

    function updateViewButtons() {

        var isVertical =
            currentView ===
            "tree-vertical";


        var isHorizontal =
            currentView ===
            "tree-horizontal";


        var isTable =
            currentView ===
            "table";


        viewTreeVerticalButton.classList.toggle(
            "active",
            isVertical
        );


        viewTreeHorizontalButton.classList.toggle(
            "active",
            isHorizontal
        );


        viewTableButton.classList.toggle(
            "active",
            isTable
        );


        treeView.classList.toggle(
            "active",
            !isTable
        );


        tableView.classList.toggle(
            "active",
            isTable
        );


        treeView.classList.toggle(
            "kvc-tree-vertical",
            isVertical
        );


        treeView.classList.toggle(
            "kvc-tree-horizontal",
            isHorizontal
        );


        if (collapseAllButton) {
            collapseAllButton.hidden =
                isTable;
        }

        if (expandAllButton) {
            expandAllButton.hidden =
                isTable;
        }

        if (treeActionsSeparator) {
            treeActionsSeparator.hidden =
                isTable;
        }
    }


    viewTreeVerticalButton.onclick =
        function () {

            currentView =
                "tree-vertical";

            closeAllPopups();

            updateViewButtons();

            renderCurrentView();

            scheduleKvcUiStateSave(20);

            closeKvcViewMenu();
        };


    viewTreeHorizontalButton.onclick =
        function () {

            currentView =
                "tree-horizontal";

            closeAllPopups();

            updateViewButtons();

            renderCurrentView();

            scheduleKvcUiStateSave(20);

            closeKvcViewMenu();
        };


    viewTableButton.onclick =
        function () {

            currentView =
                "table";

            closeAllPopups();

            updateViewButtons();

            renderCurrentView();

            scheduleKvcUiStateSave(20);

            closeKvcViewMenu();
        };


    if (collapseAllButton) {

        collapseAllButton.onclick =
            function () {

                if (!treeData) {
                    return;
                }

                closeAllPopups();

                /*
                 * Полностью пересобираем состояние сворачивания:
                 * корневой КВЦ остаётся видимым, а все узлы с
                 * дочерними элементами становятся свёрнутыми.
                 */
                collapsed =
                    {};

                collapseAllByDefault(
                    treeData
                );

                renderTree();

                scheduleKvcUiStateSave(20);

                closeKvcViewMenu();
            };
    }


    if (expandAllButton) {

        expandAllButton.onclick =
            function () {

                if (!treeData) {
                    return;
                }

                closeAllPopups();

                collapsed =
                    {};

                renderTree();

                scheduleKvcUiStateSave(20);

                closeKvcViewMenu();
            };
    }


    if (viewMenuButton) {

        viewMenuButton.onclick =
            function (event) {

                event.preventDefault();
                event.stopPropagation();

                if (viewMenu) {
                    viewMenu.hidden =
                        !viewMenu.hidden;
                }
            };
    }


    if (viewMenu) {

        viewMenu.onclick =
            function (event) {
                event.stopPropagation();
            };
    }


    document.addEventListener(
        "pointerdown",
        function (event) {

            if (
                !viewMenu ||
                viewMenu.hidden ||
                !viewMenuButton
            ) {
                return;
            }

            if (
                !viewMenu.contains(event.target) &&
                !viewMenuButton.contains(event.target)
            ) {
                closeKvcViewMenu();
            }
        }
    );


    function renderCurrentView() {

        if (
            currentView ===
            "table"
        ) {

            renderTable();

        } else {

            renderTree();
        }
    }


    /* =========================================================
       ФОРМА ПОКАЗАТЕЛЯ
       ========================================================= */

    function updateIndicatorFields() {

        var type =
            editIndicatorType.value;


        booleanFields
            .classList
            .remove("visible");


        numericFields
            .classList
            .remove("visible");


        if (
            type === "boolean"
        ) {

            booleanFields
                .classList
                .add("visible");

            return;
        }


        if (
            isNumericIndicator(type)
        ) {

            var unit =
                getIndicatorUnit(type);


            rangeFromLabel.textContent =
                "с (X)" +
                (
                    unit
                        ? " " + unit
                        : ""
                );


            rangeToLabel.textContent =
                "до (Y)" +
                (
                    unit
                        ? " " + unit
                        : ""
                );


            numericFields
                .classList
                .add("visible");
        }
    }


    /* =========================================================
       BOOLEAN
       ========================================================= */

    function getAdminTrueLabel() {

        return (
            editTrueLabel.value.trim() ||
            "Да"
        );
    }


    function getAdminFalseLabel() {

        return (
            editFalseLabel.value.trim() ||
            "Нет"
        );
    }


    function getNodeTrueLabel(node) {

        return (
            node.trueLabel ||
            "Да"
        );
    }


    function getNodeFalseLabel(node) {

        return (
            node.falseLabel ||
            "Нет"
        );
    }


    function buildBooleanSelect(
        select,
        trueLabel,
        falseLabel,
        currentValue
    ) {

        select.innerHTML = "";


        var trueOption =
            document.createElement(
                "option"
            );

        trueOption.value =
            "true";

        trueOption.textContent =
            trueLabel;

        select.appendChild(
            trueOption
        );


        var falseOption =
            document.createElement(
                "option"
            );

        falseOption.value =
            "false";

        falseOption.textContent =
            falseLabel;

        select.appendChild(
            falseOption
        );


        var value =
            currentValue !== null &&
            currentValue !== undefined
                ? String(currentValue)
                : "";


        if (
            value === falseLabel ||
            value === "false" ||
            value === "0"
        ) {

            select.value =
                "false";

        } else {

            select.value =
                "true";
        }
    }


    function updateAdminCurrentValueField(
        originalValue
    ) {

        var type =
            editIndicatorType.value;


        if (
            type === "boolean"
        ) {

            editCurrentValueText
                .classList
                .add("hidden");


            editCurrentValueBoolean
                .classList
                .add("visible");


            buildBooleanSelect(
                editCurrentValueBoolean,
                getAdminTrueLabel(),
                getAdminFalseLabel(),
                originalValue
            );

        } else {

            editCurrentValueBoolean
                .classList
                .remove("visible");


            editCurrentValueText
                .classList
                .remove("hidden");


            if (
                originalValue !== undefined
            ) {

                editCurrentValueText.value =
                    originalValue !== null
                        ? originalValue
                        : "";
            }
        }
    }


    function updateUserCurrentValueField(node) {

        if (
            node.indicatorType ===
            "boolean"
        ) {

            userCurrentTextWrap
                .classList
                .add("hidden");


            userCurrentValueBoolean
                .classList
                .add("visible");


            buildBooleanSelect(
                userCurrentValueBoolean,
                getNodeTrueLabel(node),
                getNodeFalseLabel(node),
                node.currentValue
            );


            return;
        }


        userCurrentValueBoolean
            .classList
            .remove("visible");


        userCurrentTextWrap
            .classList
            .remove("hidden");


        userCurrentValueText.value =
            node.currentValue !== null &&
            node.currentValue !== undefined
                ? node.currentValue
                : "";


        var unit =
            getIndicatorUnit(
                node.indicatorType
            );


        userCurrentValueUnit.textContent =
            unit;


        userCurrentTextWrap
            .classList
            .toggle(
                "has-unit",
                !!unit
            );


        userCurrentValueUnit.style.display =
            unit
                ? "block"
                : "none";
    }


    /* =========================================================
       ИКОНКИ
       ========================================================= */

    function createKvcIconButton(
        icon,
        title
    ) {

        var button =
            document.createElement(
                "button"
            );


        button.type =
            "button";


        button.className =
            "kvc-icon-button";


        var iconMap = {
            info: "ti ti-info-circle",
            calendar: "ti ti-calendar",
            users: "ti ti-users",
            settings: "ti ti-settings"
        };


        var iconElement =
            document.createElement(
                "i"
            );


        iconElement.className =
            iconMap[icon] || "ti ti-circle";


        button.appendChild(
            iconElement
        );


        button.title =
            title || "";


        return button;
    }


    /* =========================================================
       POPUPS
       ========================================================= */

    function closeAllPopups() {

        var popups =
            widget.querySelectorAll(
                ".kvc-popup"
            );


        for (
            var i = 0;
            i < popups.length;
            i++
        ) {

            if (
                popups[i].parentNode
            ) {

                popups[i]
                    .parentNode
                    .removeChild(
                        popups[i]
                    );
            }
        }


        var activeButtons =
            widget.querySelectorAll(
                ".kvc-icon-button.active"
            );


        for (
            var j = 0;
            j < activeButtons.length;
            j++
        ) {

            activeButtons[j]
                .classList
                .remove("active");
        }
    }


    /* =========================================================
       НОВОЕ: ДОПОЛНИТЕЛЬНО
       ========================================================= */

    function openInfoPopup(
        wrap,
        button,
        node
    ) {

        var wasActive =
            button.classList.contains(
                "active"
            );


        closeAllPopups();


        if (
            wasActive
        ) {
            return;
        }


        button.classList.add(
            "active"
        );


        var popup =
            document.createElement(
                "div"
            );


        popup.className =
            "kvc-popup kvc-info-popup";


        var title =
            document.createElement(
                "div"
            );


        title.className =
            "kvc-popup-title";


        title.textContent =
            "Дополнительно";


        popup.appendChild(
            title
        );


        var text =
            document.createElement(
                "div"
            );


        text.className =
            "kvc-info-text";


        if (
            node.additional &&
            String(node.additional).trim()
        ) {

            text.textContent =
                node.additional;

        } else {

            text.textContent =
                "Дополнительная информация не указана";


            text.classList.add(
                "kvc-info-empty"
            );
        }


        popup.appendChild(
            text
        );


        popup.onclick =
            function (event) {

                event.stopPropagation();

            };


        wrap.appendChild(
            popup
        );
    }


    /* =========================================================
       КАЛЕНДАРЬ
       ========================================================= */

    function openCalendarPopup(
        wrap,
        button,
        node
    ) {

        var wasActive =
            button.classList.contains(
                "active"
            );


        closeAllPopups();


        if (
            wasActive
        ) {
            return;
        }


        button.classList.add(
            "active"
        );


        var popup =
            document.createElement(
                "div"
            );


        popup.className =
            "kvc-popup kvc-date-popup";


        popup.innerHTML =

            '<div class="kvc-popup-title">' +
                'Период КВЦ' +
            '</div>' +

            '<div class="kvc-date-grid">' +

                '<div class="kvc-date-row">' +

                    '<div class="kvc-date-label">' +
                        'Дата начала' +
                    '</div>' +

                    '<div class="kvc-date-value">' +
                        escapeHtml(
                            formatDate(
                                node.startDate
                            )
                        ) +
                    '</div>' +

                '</div>' +

                '<div class="kvc-date-row">' +

                    '<div class="kvc-date-label">' +
                        'Дата окончания' +
                    '</div>' +

                    '<div class="kvc-date-value">' +
                        escapeHtml(
                            formatDate(
                                node.endDate
                            )
                        ) +
                    '</div>' +

                '</div>' +

            '</div>' +

            '<div class="kvc-date-last-row">' +

                '<div class="kvc-date-label">' +
                    'Дата последнего внесения значения' +
                '</div>' +

                '<div class="kvc-date-value">' +
                    escapeHtml(
                        formatDate(
                            node.lastValueDate
                        )
                    ) +
                '</div>' +

            '</div>';


        popup.onclick =
            function (event) {

                event.stopPropagation();

            };


        wrap.appendChild(
            popup
        );
    }


    /* =========================================================
       УЧАСТНИКИ
       ========================================================= */

    function collectKvcParticipants(node) {

        var result = [];

        var seenById = {};
        var seenByName = {};


        function addUser(user) {

            if (
                user === null ||
                user === undefined
            ) {
                return;
            }


            var userId = "";

            var userName = "";


            if (
                typeof user === "string"
            ) {

                userName =
                    String(user).trim();

            } else {

                userId =
                    user.id !== null &&
                    user.id !== undefined
                        ? String(
                            user.id
                        ).trim()
                        : "";


                userName =
                    user.fio ||
                    user.name ||
                    user.owner ||
                    "";


                userName =
                    String(
                        userName
                    ).trim();
            }


            if (!userName) {
                return;
            }


            if (
                userId &&
                seenById[userId]
            ) {
                return;
            }


            var nameKey =
                userName.toLowerCase();


            if (
                seenByName[nameKey]
            ) {
                return;
            }


            if (userId) {
                seenById[userId] = true;
            }


            seenByName[nameKey] = true;


            result.push(
                user
            );
        }


        function addNodeParticipants(
            currentNode
        ) {

            if (!currentNode) {
                return;
            }


            if (
                currentNode.owner
            ) {

                addUser({
                    id:
                        currentNode.ownerId ||
                        "",

                    name:
                        currentNode.owner
                });
            }


            var ownUsers =
                currentNode.users ||
                [];


            for (
                var i = 0;
                i < ownUsers.length;
                i++
            ) {

                addUser(
                    ownUsers[i]
                );
            }
        }


        /*
         * Собираем участников только текущего узла
         * и его непосредственных детей.
         * Во внуков и более глубокие уровни не идём.
         */

        addNodeParticipants(
            node
        );


        var children =
            node && node.children
                ? node.children
                : [];


        for (
            var i = 0;
            i < children.length;
            i++
        ) {

            addNodeParticipants(
                children[i]
            );
        }


        return result;
    }


    function openUsersPopup(
        wrap,
        button,
        node
    ) {

        var wasActive =
            button.classList.contains(
                "active"
            );


        closeAllPopups();


        if (
            wasActive
        ) {
            return;
        }


        button.classList.add(
            "active"
        );


        var popup =
            document.createElement(
                "div"
            );


        popup.className =
            "kvc-popup kvc-users-popup";


        var title =
            document.createElement(
                "div"
            );


        title.className =
            "kvc-popup-title";


        title.textContent =
            "Участники";


        popup.appendChild(
            title
        );


        var list =
            document.createElement(
                "div"
            );


        list.className =
            "kvc-user-list";


        var users =
            collectKvcParticipants(
                node
            );


        if (
            users.length === 0
        ) {

            var empty =
                document.createElement(
                    "div"
                );


            empty.style.fontSize =
                "11px";


            empty.style.color =
                "#929baa";


            empty.textContent =
                "Участники не указаны";


            list.appendChild(
                empty
            );
        }


        for (
            var i = 0;
            i < users.length;
            i++
        ) {

            var item =
                document.createElement(
                    "div"
                );


            item.className =
                "kvc-user-item";


            var link =
                document.createElement(
                    "a"
                );


            link.href =
                "#";


            link.className =
                "kvc-user-link";


            var userItem =
                users[i];


            link.textContent =
                typeof userItem === "string"
                    ? userItem
                    : (
                        userItem &&
                        (
                            userItem.fio ||
                            userItem.name
                        )
                            ? (
                                userItem.fio ||
                                userItem.name
                            )
                            : ""
                    );


            link.onclick =
                function (event) {

                    event.preventDefault();

                    event.stopPropagation();

                };


            item.appendChild(
                link
            );


            list.appendChild(
                item
            );
        }


        popup.appendChild(
            list
        );


        popup.onclick =
            function (event) {

                event.stopPropagation();

            };


        wrap.appendChild(
            popup
        );
    }


    /* =========================================================
       OWNER
       ========================================================= */

    function setOwnerControl(name) {

        if (
            name
        ) {

            ownerControl.textContent =
                name;


            ownerControl
                .classList
                .remove(
                    "kvc-owner-placeholder"
                );

        } else {

            ownerControl.textContent =
                "Выберите владельца";


            ownerControl
                .classList
                .add(
                    "kvc-owner-placeholder"
                );
        }
    }


    function positionOwnerDropdown() {

        if (
            !ownerDropdown
                .classList
                .contains(
                    "open"
                )
        ) {
            return;
        }


        var rect =
            ownerControl
                .getBoundingClientRect();


        var left =
            rect.left;


        var top =
            rect.bottom + 4;


        var width =
            rect.width;


        if (
            left + width >
            window.innerWidth - 10
        ) {

            left =
                window.innerWidth -
                width -
                10;
        }


        if (
            left < 10
        ) {

            left =
                10;
        }


        ownerDropdown.style.left =
            left + "px";


        ownerDropdown.style.top =
            top + "px";


        ownerDropdown.style.width =
            width + "px";
    }


    function openOwnerDropdown() {

        if (
            !isAdmin
        ) {
            return;
        }


        ownerDropdown
            .classList
            .add("open");


        ownerSearch.value =
            "";


        renderOwnerOptions(
            ""
        );


        positionOwnerDropdown();


        setTimeout(
            function () {

                ownerSearch.focus();

            },
            0
        );
    }


    function closeOwnerDropdown() {

        ownerDropdown
            .classList
            .remove("open");
    }


    function renderOwnerOptions(
        searchText
    ) {

        ownerOptions.innerHTML =
            "";


        var search =
            String(
                searchText || ""
            )
                .toLowerCase()
                .trim();


        for (
            var i = 0;
            i < allUsers.length;
            i++
        ) {

            var user =
                allUsers[i];


            if (
                search &&
                user.name
                    .toLowerCase()
                    .indexOf(search) === -1
            ) {

                continue;
            }


            var option =
                document.createElement(
                    "div"
                );


            option.className =
                "kvc-owner-option";


            option.textContent =
                user.name;


            option.onclick =
                (function (
                    selectedUser
                ) {

                    return function (
                        event
                    ) {

                        event.stopPropagation();


                        selectedOwnerId =
                            selectedUser.id;


                        selectedOwnerName =
                            selectedUser.name;


                        setOwnerControl(
                            selectedOwnerName
                        );


                        closeOwnerDropdown();

                    };

                })(user);


            ownerOptions.appendChild(
                option
            );
        }
    }


    /* =========================================================
       ПРАВА НА СОБРАНИЯ
       ========================================================= */

    function normalizeMeetingManagerIds(
        node
    ) {

        var source =
            node && Array.isArray(
                node.meetingManagerIds
            )
                ? node.meetingManagerIds
                : [];

        var result = [];
        var seen = {};

        for (
            var i = 0;
            i < source.length;
            i++
        ) {

            var id =
                source[i] !== null &&
                source[i] !== undefined
                    ? String(source[i])
                    : "";

            if (
                !id ||
                seen[id]
            ) {
                continue;
            }

            seen[id] = true;
            result.push(id);
        }

        return result;
    }


    function isMeetingManagerSelected(
        userId
    ) {

        var id =
            userId !== null &&
            userId !== undefined
                ? String(userId)
                : "";

        if (!id) {
            return false;
        }

        for (
            var i = 0;
            i < selectedMeetingManagerIds.length;
            i++
        ) {

            if (
                String(
                    selectedMeetingManagerIds[i]
                ) === id
            ) {
                return true;
            }
        }

        return false;
    }


    function setMeetingManagerSelected(
        userId,
        selected
    ) {

        var id =
            userId !== null &&
            userId !== undefined
                ? String(userId)
                : "";

        if (!id) {
            return;
        }

        var next = [];

        for (
            var i = 0;
            i < selectedMeetingManagerIds.length;
            i++
        ) {

            if (
                String(
                    selectedMeetingManagerIds[i]
                ) !== id
            ) {
                next.push(
                    String(
                        selectedMeetingManagerIds[i]
                    )
                );
            }
        }

        if (selected) {
            next.push(id);
        }

        selectedMeetingManagerIds =
            next;


        renderMeetingManagerTags();
    }


    function getMeetingManagerName(
        userId
    ) {

        var id =
            userId !== null &&
            userId !== undefined
                ? String(userId)
                : "";


        for (
            var i = 0;
            i < allUsers.length;
            i++
        ) {

            if (
                String(
                    allUsers[i].id
                ) === id
            ) {

                return (
                    allUsers[i].name ||
                    id
                );
            }
        }


        return id;
    }


    function renderMeetingManagerTags() {

        meetingManagersTags.innerHTML =
            "";


        meetingManagersPlaceholder.style.display =
            selectedMeetingManagerIds.length
                ? "none"
                : "block";


        for (
            var i = 0;
            i < selectedMeetingManagerIds.length;
            i++
        ) {

            var userId =
                String(
                    selectedMeetingManagerIds[i]
                );


            var tag =
                document.createElement(
                    "span"
                );


            tag.className =
                "kvc-meeting-manager-tag";


            var name =
                document.createElement(
                    "span"
                );


            name.className =
                "kvc-meeting-manager-tag-name";


            name.textContent =
                getMeetingManagerName(
                    userId
                );


            var remove =
                document.createElement(
                    "button"
                );


            remove.type =
                "button";


            remove.className =
                "kvc-meeting-manager-tag-remove";


            remove.textContent =
                "×";


            remove.title =
                "Удалить";


            remove.onclick =
                (function (
                    selectedUserId
                ) {

                    return function (
                        event
                    ) {

                        event.preventDefault();
                        event.stopPropagation();


                        setMeetingManagerSelected(
                            selectedUserId,
                            false
                        );


                        renderMeetingManagerOptions(
                            meetingManagersSearch.value
                        );
                    };

                })(userId);


            tag.appendChild(
                name
            );


            tag.appendChild(
                remove
            );


            meetingManagersTags.appendChild(
                tag
            );
        }
    }


    function positionMeetingManagersDropdown() {

        if (
            !meetingManagersDropdown
                .classList
                .contains(
                    "open"
                )
        ) {
            return;
        }


        var rect =
            meetingManagersSelection
                .getBoundingClientRect();


        var left =
            rect.left;


        var top =
            rect.bottom + 4;


        var width =
            rect.width;


        if (
            left + width >
            window.innerWidth - 10
        ) {

            left =
                window.innerWidth -
                width -
                10;
        }


        if (left < 10) {
            left = 10;
        }


        meetingManagersDropdown.style.left =
            left + "px";


        meetingManagersDropdown.style.top =
            top + "px";


        meetingManagersDropdown.style.width =
            width + "px";
    }


    function openMeetingManagersDropdown() {

        meetingManagersDropdown
            .classList
            .add(
                "open"
            );


        meetingManagersSelection
            .classList
            .add(
                "open"
            );


        meetingManagersSearch.value =
            "";


        renderMeetingManagerOptions(
            ""
        );


        positionMeetingManagersDropdown();


        setTimeout(
            function () {

                meetingManagersSearch.focus();

            },
            0
        );
    }


    function closeMeetingManagersDropdown() {

        meetingManagersDropdown
            .classList
            .remove(
                "open"
            );


        meetingManagersSelection
            .classList
            .remove(
                "open"
            );
    }


    function renderMeetingManagerOptions(
        searchText
    ) {

        meetingManagersOptions.innerHTML =
            "";

        var search =
            String(
                searchText || ""
            )
                .toLowerCase()
                .trim();

        var visibleCount = 0;

        for (
            var i = 0;
            i < allUsers.length;
            i++
        ) {

            var user =
                allUsers[i];

            var name =
                String(
                    user.name || ""
                );

            if (
                search &&
                name
                    .toLowerCase()
                    .indexOf(search) === -1
            ) {
                continue;
            }

            visibleCount++;

            var label =
                document.createElement(
                    "label"
                );

            label.className =
                "kvc-meeting-manager-option";

            var checkbox =
                document.createElement(
                    "input"
                );

            checkbox.type =
                "checkbox";

            checkbox.className =
                "kvc-meeting-manager-checkbox";

            checkbox.checked =
                isMeetingManagerSelected(
                    user.id
                );

            checkbox.onchange =
                (function (
                    selectedUser,
                    selectedCheckbox
                ) {

                    return function () {

                        setMeetingManagerSelected(
                            selectedUser.id,
                            selectedCheckbox.checked
                        );
                    };

                })(
                    user,
                    checkbox
                );

            var text =
                document.createElement(
                    "span"
                );

            text.textContent =
                name;

            label.appendChild(
                checkbox
            );

            label.appendChild(
                text
            );

            meetingManagersOptions.appendChild(
                label
            );
        }

        if (!visibleCount) {

            var empty =
                document.createElement(
                    "div"
                );

            empty.className =
                "kvc-meeting-manager-empty";

            empty.textContent =
                "Пользователи не найдены";

            meetingManagersOptions.appendChild(
                empty
            );
        }
    }


    function updateMeetingManagersVisibility(
        node,
        createMode
    ) {

        var nodeType =
            createMode
                ? editNodeType.value
                : (
                    node
                        ? node.type
                        : ""
                );

        meetingManagersRow.classList.toggle(
            "hidden",
            nodeType === "op"
        );


        if (
            nodeType === "op"
        ) {
            closeMeetingManagersDropdown();
        }
    }


    function canCurrentUserManageMeetingsLocal(
        node
    ) {

        if (
            !node ||
            !currentUserData ||
            node.type === "op"
        ) {
            return false;
        }

        var currentUserId =
            currentUserData.id !== null &&
            currentUserData.id !== undefined
                ? String(
                    currentUserData.id
                )
                : "";

        var currentUserName =
            String(
                currentUserData.fio ||
                currentUserData.name ||
                ""
            ).trim();

        if (
            node.ownerId &&
            currentUserId &&
            String(node.ownerId) ===
                currentUserId
        ) {
            return true;
        }

        if (
            node.owner &&
            currentUserName &&
            String(node.owner).trim() ===
                currentUserName
        ) {
            return true;
        }

        var managerIds =
            normalizeMeetingManagerIds(
                node
            );

        for (
            var i = 0;
            i < managerIds.length;
            i++
        ) {

            if (
                currentUserId &&
                String(managerIds[i]) ===
                    currentUserId
            ) {
                return true;
            }
        }

        return false;
    }


    /* =========================================================
       SETTINGS
       ========================================================= */

    function openSettings(
        node,
        createMode
    ) {

        closeAllPopups();

        closeOwnerDropdown();


        currentSettingsNode =
            node;


        var isCreateMode =
            !!createMode;


        if (
            isAdmin
        ) {

            settingsTitle.textContent =
                isCreateMode
                    ? "Добавление КВЦ / ОП"
                    : (
                        node.typeName === "ОП"
                            ? "Настройки ОП"
                            : "Настройки КВЦ"
                    );


            settingsModal
                .classList
                .remove(
                    "user-mode"
                );


            adminSettings
                .classList
                .add(
                    "visible"
                );


            userSettings
                .classList
                .remove(
                    "visible"
                );


            createTypeRow.classList.toggle(
                "visible",
                isCreateMode
            );


            if (isCreateMode) {

                editNodeType.value =
                    node.type === "op"
                        ? "op"
                        : "kvc-op";
            }


            settingsStructureActions.style.display =
                isCreateMode
                    ? "none"
                    : "flex";


            settingsAdd.style.display =
                !isCreateMode &&
                node.type !== "op"
                    ? "inline-flex"
                    : "none";


            settingsDelete.style.display =
                !isCreateMode &&
                treeData &&
                String(node.id) !== String(treeData.id)
                    ? "inline-flex"
                    : "none";


            editDescription.value =
                node.description ||
                "";


            /* НОВОЕ */

            editAdditional.value =
                node.additional ||
                "";


            editIndicatorType.value =
                node.indicatorType ||
                "percent";


            editTrueLabel.value =
                node.trueLabel ||
                "Да";


            editFalseLabel.value =
                node.falseLabel ||
                "Нет";


            editRangeFrom.value =
                node.rangeFrom !== null &&
                node.rangeFrom !== undefined
                    ? node.rangeFrom
                    : "";


            editRangeTo.value =
                node.rangeTo !== null &&
                node.rangeTo !== undefined
                    ? node.rangeTo
                    : "";


            editStartDate.value =
                node.startDate ||
                "";


            editEndDate.value =
                node.endDate ||
                "";


            editLastValueDate.value =
                node.lastValueDate ||
                "";


            editCurrentValueText.value =
                node.currentValue !== null &&
                node.currentValue !== undefined
                    ? node.currentValue
                    : "";


            selectedOwnerId =
                node.ownerId ||
                null;


            selectedOwnerName =
                node.owner ||
                "";


            setOwnerControl(
                selectedOwnerName
            );


            selectedMeetingManagerIds =
                normalizeMeetingManagerIds(
                    node
                );


            meetingManagersSearch.value =
                "";


            renderMeetingManagerTags();


            renderMeetingManagerOptions(
                ""
            );


            closeMeetingManagersDropdown();


            updateMeetingManagersVisibility(
                node,
                isCreateMode
            );


            updateIndicatorFields();


            updateAdminCurrentValueField(
                node.currentValue
            );

        } else {

            settingsTitle.textContent =
                "Текущее значение";


            settingsModal
                .classList
                .add(
                    "user-mode"
                );


            adminSettings
                .classList
                .remove(
                    "visible"
                );


            userSettings
                .classList
                .add(
                    "visible"
                );


            settingsStructureActions.style.display =
                "none";


            meetingManagersRow.classList.add(
                "hidden"
            );


            createTypeRow.classList.remove(
                "visible"
            );


            updateUserCurrentValueField(
                node
            );
        }


        settingsOverlay
            .classList
            .add(
                "open"
            );
    }


    function closeSettings() {

        closeOwnerDropdown();


        settingsOverlay
            .classList
            .remove(
                "open"
            );


        currentSettingsNode =
            null;


        isCreatingNode =
            false;


        creatingParentNode =
            null;


        selectedMeetingManagerIds =
            [];


        meetingManagersSearch.value =
            "";


        meetingManagersTags.innerHTML =
            "";


        meetingManagersPlaceholder.style.display =
            "block";


        meetingManagersOptions.innerHTML =
            "";


        closeMeetingManagersDropdown();


        meetingManagersRow.classList.add(
            "hidden"
        );


        createTypeRow.classList.remove(
            "visible"
        );
    }


    /* =========================================================
       ДОБАВЛЕНИЕ / УДАЛЕНИЕ КВЦ / ОП
       ========================================================= */

    function findParentNodeLocal(
        node,
        childId
    ) {

        if (
            !node ||
            !Array.isArray(node.children)
        ) {
            return null;
        }


        for (
            var i = 0;
            i < node.children.length;
            i++
        ) {

            var child =
                node.children[i];


            if (
                String(child.id) ===
                String(childId)
            ) {
                return node;
            }


            var found =
                findParentNodeLocal(
                    child,
                    childId
                );


            if (found) {
                return found;
            }
        }


        return null;
    }


    function createNewChildNode() {

        return {

            id:
                "kvc-" +
                Date.now() +
                "-" +
                Math.floor(
                    Math.random() *
                    100000
                ),

            type:
                "kvc-op",

            typeName:
                "↑ОП / КВЦ↓",

            title:
                "",

            description:
                "",

            additional:
                "",

            indicatorType:
                "percent",

            rangeFrom:
                0,

            rangeTo:
                100,

            trueLabel:
                "Да",

            falseLabel:
                "Нет",

            currentValue:
                "",

            autoaudit:
                "—",

            ownerId:
                null,

            owner:
                "",

            startDate:
                "",

            endDate:
                "",

            lastValueDate:
                "",

            users:
                [],

            children:
                []
        };
    }


    function addChildFromSettings() {

        if (
            !isAdmin ||
            !currentSettingsNode ||
            !treeData ||
            currentSettingsNode.type === "op"
        ) {
            return;
        }


        /*
         * Ничего сразу не сохраняем.
         * Запоминаем родителя и открываем обычное окно настроек
         * для нового элемента. Вверху появляется выбор: КВЦ / ОП.
         */

        creatingParentNode =
            currentSettingsNode;


        isCreatingNode =
            true;


        var newNode =
            createNewChildNode();


        openSettings(
            newNode,
            true
        );
    }


    async function deleteNodeFromSettings() {

        if (
            !isAdmin ||
            !currentSettingsNode ||
            !treeData ||
            String(currentSettingsNode.id) ===
                String(treeData.id)
        ) {
            return;
        }


        var nodeId =
            String(
                currentSettingsNode.id
            );


        var nodeName =
            currentSettingsNode.description ||
            currentSettingsNode.title ||
            "КВЦ / ОП";


        if (
            !window.confirm(
                "Удалить «" +
                nodeName +
                "» и все его дочерние элементы?"
            )
        ) {
            return;
        }


        var parentNode =
            findParentNodeLocal(
                treeData,
                nodeId
            );


        if (
            !parentNode ||
            !Array.isArray(
                parentNode.children
            )
        ) {

            alert(
                "Не удалось найти родительский КВЦ / ОП"
            );

            return;
        }


        var oldChildren =
            parentNode.children.slice();


        parentNode.children =
            parentNode.children.filter(
                function (child) {

                    return (
                        String(child.id) !==
                        nodeId
                    );
                }
            );


        settingsDelete.disabled =
            true;


        try {

            await window.KvcApi.updateKVC(
                treeData
            );


            var freshTree =
                await window.KvcApi.getKVC();


            if (freshTree) {
                treeData = freshTree;
            }


            closeSettings();

            renderCurrentView();

        } catch (error) {

            parentNode.children =
                oldChildren;


            console.error(
                "Ошибка удаления КВЦ / ОП",
                error
            );


            alert(
                error && error.message
                    ? error.message
                    : "Не удалось удалить КВЦ / ОП"
            );


            renderCurrentView();

        } finally {

            settingsDelete.disabled =
                false;
        }
    }


    /* =========================================================
       SAVE
       ========================================================= */

    async function saveSettings() {

        if (
            !currentSettingsNode ||
            !treeData
        ) {
            return;
        }


        var nodeId =
            String(
                currentSettingsNode.id
            );


        var newCurrentValue;


        var savingNewNode =
            isAdmin &&
            isCreatingNode;


        var newNodeParent =
            savingNewNode
                ? creatingParentNode
                : null;


        var newNodeParentId =
            newNodeParent
                ? String(newNodeParent.id)
                : null;


        var newNodeInserted =
            false;


        settingsSave.disabled =
            true;


        settingsSave.textContent =
            "Сохранение...";


        try {

            /* =================================================
               АДМИН
               ================================================= */

            if (
                isAdmin
            ) {

                if (
                    savingNewNode
                ) {

                    currentSettingsNode.type =
                        editNodeType.value === "op"
                            ? "op"
                            : "kvc-op";


                    currentSettingsNode.typeName =
                        currentSettingsNode.type === "op"
                            ? "ОП"
                            : "↑ОП / КВЦ↓";


                    if (
                        currentSettingsNode.type === "op"
                    ) {

                        currentSettingsNode.children =
                            [];
                    }
                }


                currentSettingsNode.description =
                    editDescription
                        .value
                        .trim();


                /*
                 * НОВОЕ ПОЛЕ "ДОПОЛНИТЕЛЬНО".
                 * Оно попадёт в общий JSON дерева и сохранится
                 * через updateKVC(treeData).
                 */

                currentSettingsNode.additional =
                    editAdditional
                        .value
                        .trim();


                currentSettingsNode.indicatorType =
                    editIndicatorType.value;


                if (
                    editIndicatorType.value ===
                    "boolean"
                ) {

                    currentSettingsNode.trueLabel =
                        getAdminTrueLabel();


                    currentSettingsNode.falseLabel =
                        getAdminFalseLabel();


                    currentSettingsNode.rangeFrom =
                        null;


                    currentSettingsNode.rangeTo =
                        null;


                    newCurrentValue =
                        editCurrentValueBoolean.value ===
                        "false"
                            ? getAdminFalseLabel()
                            : getAdminTrueLabel();

                } else {

                    currentSettingsNode.rangeFrom =
                        editRangeFrom.value !== ""
                            ? Number(
                                editRangeFrom.value
                            )
                            : null;


                    currentSettingsNode.rangeTo =
                        editRangeTo.value !== ""
                            ? Number(
                                editRangeTo.value
                            )
                            : null;


                    newCurrentValue =
                        editCurrentValueText
                            .value
                            .trim();
                }


                currentSettingsNode.currentValue =
                    newCurrentValue;


                currentSettingsNode.startDate =
                    editStartDate.value;


                currentSettingsNode.endDate =
                    editEndDate.value;


                currentSettingsNode.lastValueDate =
                    editLastValueDate.value;


                currentSettingsNode.ownerId =
                    selectedOwnerId;


                currentSettingsNode.owner =
                    selectedOwnerName;


                if (
                    currentSettingsNode.type === "op"
                ) {

                    currentSettingsNode.meetingManagerIds =
                        [];

                } else {

                    currentSettingsNode.meetingManagerIds =
                        selectedMeetingManagerIds.slice();
                }


                if (
                    savingNewNode
                ) {

                    if (
                        !newNodeParent ||
                        newNodeParent.type === "op"
                    ) {

                        throw new Error(
                            "Невозможно добавить дочерний элемент"
                        );
                    }


                    if (
                        !Array.isArray(
                            newNodeParent.children
                        )
                    ) {

                        newNodeParent.children =
                            [];
                    }


                    newNodeParent.children.push(
                        currentSettingsNode
                    );


                    newNodeInserted =
                        true;
                }


                /*
                 * Сохраняем ВСЁ реальное дерево.
                 * updateKVC в вашем TypeScript рассчитан именно
                 * на структуру целиком.
                 */

                await window.KvcApi.updateKVC(
                    treeData
                );


                /*
                 * Текущее значение хранится отдельно
                 * в kvc_value_<ID>, поэтому записываем его
                 * отдельным вызовом.
                 */

                await window.KvcApi.updateKVCValue(
                    nodeId,
                    newCurrentValue,
                    true
                );

            }

            /* =================================================
               НЕ АДМИН
               ================================================= */

            else {

                if (
                    currentSettingsNode
                        .indicatorType ===
                    "boolean"
                ) {

                    newCurrentValue =
                        userCurrentValueBoolean.value ===
                        "false"
                            ? getNodeFalseLabel(
                                currentSettingsNode
                            )
                            : getNodeTrueLabel(
                                currentSettingsNode
                            );

                } else {

                    /*
                     * Единица измерения в значение не попадает.
                     * Сохраняется только введённое значение.
                     */

                    newCurrentValue =
                        userCurrentValueText
                            .value
                            .trim();
                }


                await window.KvcApi.updateKVCValue(
                    nodeId,
                    newCurrentValue,
                    false
                );
            }


            /*
             * После сохранения перечитываем дерево из Laravel,
             * чтобы получить актуальные currentValue и
             * lastValueDate из отдельных записей в базе данных.
             */

            var freshTree =
                await window.KvcApi.getKVC();


            if (
                freshTree
            ) {

                treeData =
                    freshTree;
            }


            if (
                savingNewNode &&
                newNodeParentId
            ) {

                delete collapsed[
                    newNodeParentId
                ];
            }


            closeSettings();


            renderCurrentView();

        } catch (error) {

            if (
                savingNewNode &&
                newNodeInserted &&
                newNodeParent &&
                Array.isArray(
                    newNodeParent.children
                )
            ) {

                for (
                    var rollbackIndex =
                        newNodeParent.children.length - 1;
                    rollbackIndex >= 0;
                    rollbackIndex--
                ) {

                    if (
                        String(
                            newNodeParent.children[rollbackIndex].id
                        ) === nodeId
                    ) {

                        newNodeParent.children.splice(
                            rollbackIndex,
                            1
                        );

                        break;
                    }
                }
            }


            console.error(
                "Ошибка сохранения КВЦ / ОП",
                error
            );


            var message =
                error && error.message
                    ? error.message
                    : "Не удалось сохранить изменения";


            alert(
                message
            );

        } finally {

            settingsSave.disabled =
                false;


            settingsSave.textContent =
                "Сохранить";
        }
    }


    /* =========================================================
       СОБРАНИЯ
       ========================================================= */

    function cloneLocalJson(value) {

        return JSON.parse(
            JSON.stringify(
                value
            )
        );
    }


    function getMeetingNodeName(node) {

        if (!node) {
            return "КВЦ / ОП";
        }

        return (
            node.description ||
            node.title ||
            node.typeName ||
            "КВЦ / ОП"
        );
    }


    function padMeetingNumber(value) {

        return value < 10
            ? "0" + value
            : String(value);
    }


    function getNowDateTimeLocal() {

        var now =
            new Date();

        return (
            now.getFullYear() +
            "-" +
            padMeetingNumber(
                now.getMonth() + 1
            ) +
            "-" +
            padMeetingNumber(
                now.getDate()
            ) +
            "T" +
            padMeetingNumber(
                now.getHours()
            ) +
            ":" +
            padMeetingNumber(
                now.getMinutes()
            )
        );
    }


    function normalizeMeetingDateTime(value) {

        if (!value) {
            return "";
        }

        var text =
            String(value);

        if (
            text.length >= 16
        ) {
            return text.substring(
                0,
                16
            );
        }

        return text;
    }


    function formatMeetingDateTime(value) {

        if (!value) {
            return "—";
        }

        var date =
            new Date(value);

        if (
            isNaN(
                date.getTime()
            )
        ) {
            return String(value);
        }

        return (
            padMeetingNumber(
                date.getDate()
            ) +
            "." +
            padMeetingNumber(
                date.getMonth() + 1
            ) +
            "." +
            date.getFullYear() +
            " " +
            padMeetingNumber(
                date.getHours()
            ) +
            ":" +
            padMeetingNumber(
                date.getMinutes()
            )
        );
    }


    function formatMeetingDateOnly(value) {

        if (!value) {
            return "—";
        }

        var date =
            new Date(value);

        if (
            isNaN(
                date.getTime()
            )
        ) {
            return String(value);
        }

        return (
            padMeetingNumber(
                date.getDate()
            ) +
            "." +
            padMeetingNumber(
                date.getMonth() + 1
            ) +
            "." +
            date.getFullYear()
        );
    }


    function normalizeMeetingUser(user) {

        if (
            user === null ||
            user === undefined
        ) {
            return null;
        }

        if (
            typeof user === "string"
        ) {

            var stringName =
                String(user).trim();

            if (!stringName) {
                return null;
            }

            return {
                id: "",
                name: stringName
            };
        }

        var name =
            user.fio ||
            user.name ||
            user.owner ||
            "";

        name =
            String(name).trim();

        if (!name) {
            return null;
        }

        return {
            id:
                user.id !== null &&
                user.id !== undefined
                    ? String(user.id)
                    : "",

            name:
                name
        };
    }


    function collectMeetingParticipants(node) {

        var sourceUsers =
            collectKvcParticipants(
                node
            );

        var result = [];

        var seen = {};


        /*
         * Только для самого первого (корневого) КВЦ
         * его владелец не добавляется в собрание.
         *
         * На остальных КВЦ владелец по-прежнему является
         * участником собрания как раньше.
         */
        var isRootKvc =
            !!(
                treeData &&
                node &&
                String(node.id) ===
                    String(treeData.id)
            );


        var rootOwnerId =
            isRootKvc &&
            node.ownerId !== null &&
            node.ownerId !== undefined
                ? String(
                    node.ownerId
                ).trim()
                : "";


        var rootOwnerName =
            isRootKvc &&
            node.owner
                ? String(
                    node.owner
                )
                    .trim()
                    .toLowerCase()
                : "";


        for (
            var i = 0;
            i < sourceUsers.length;
            i++
        ) {

            var user =
                normalizeMeetingUser(
                    sourceUsers[i]
                );

            if (!user) {
                continue;
            }


            if (
                isRootKvc &&
                (
                    (
                        rootOwnerId &&
                        user.id &&
                        String(user.id).trim() ===
                            rootOwnerId
                    ) ||
                    (
                        rootOwnerName &&
                        user.name &&
                        String(user.name)
                            .trim()
                            .toLowerCase() ===
                            rootOwnerName
                    )
                )
            ) {

                continue;
            }


            var key =
                getMeetingParticipantKey(
                    user.id,
                    user.name
                );

            if (seen[key]) {
                continue;
            }

            seen[key] = true;

            result.push(
                user
            );
        }

        return result;
    }

    function getMeetingMainSectionNodeIdLocal(
        meeting
    ) {

        if (
            meeting &&
            meeting.kvcId !== null &&
            meeting.kvcId !== undefined &&
            String(meeting.kvcId)
        ) {
            return String(meeting.kvcId);
        }

        if (
            currentMeetingsNode &&
            currentMeetingsNode.id !== null &&
            currentMeetingsNode.id !== undefined
        ) {
            return String(currentMeetingsNode.id);
        }

        return "";
    }


    function getParticipantMeetingSectionIdLocal(
        participant,
        meeting
    ) {

        if (
            participant &&
            participant.sectionNodeId !== null &&
            participant.sectionNodeId !== undefined &&
            String(participant.sectionNodeId)
        ) {
            return String(participant.sectionNodeId);
        }

        return getMeetingMainSectionNodeIdLocal(
            meeting
        );
    }


    function getMeetingSectionNodeLocal(
        sectionNodeId
    ) {

        if (!sectionNodeId) {
            return null;
        }

        return findNodeByIdLocal(
            treeData,
            String(sectionNodeId)
        );
    }


    function getMeetingSectionNameLocal(
        sectionNodeId,
        fallbackName
    ) {

        var node =
            getMeetingSectionNodeLocal(
                sectionNodeId
            );

        if (node) {
            return getMeetingNodeName(
                node
            );
        }

        return fallbackName || "КВЦ";
    }


    function normalizeMeetingSectionsLocal(
        meeting
    ) {

        if (!meeting) {
            return [];
        }

        var mainSectionId =
            getMeetingMainSectionNodeIdLocal(
                meeting
            );

        var sections =
            Array.isArray(
                meeting.kvcSections
            )
                ? meeting.kvcSections
                : [];

        var normalizedSections = [];
        var seenSections = {};

        function addSection(
            nodeId,
            nodeName,
            previousMeetingId,
            previousMeetingDate
        ) {

            var id =
                nodeId !== null &&
                nodeId !== undefined
                    ? String(nodeId)
                    : "";

            if (!id || seenSections[id]) {
                return;
            }

            seenSections[id] = true;

            normalizedSections.push({
                nodeId:
                    id,
                nodeName:
                    getMeetingSectionNameLocal(
                        id,
                        nodeName || ""
                    ),
                previousMeetingId:
                    previousMeetingId || null,
                previousMeetingDate:
                    previousMeetingDate || ""
            });
        }

        /* Главный КВЦ всегда идёт первым. */
        addSection(
            mainSectionId,
            getMeetingNodeName(
                currentMeetingsNode
            ),
            meeting.previousMeetingId,
            meeting.previousMeetingDate
        );

        for (
            var sectionIndex = 0;
            sectionIndex < sections.length;
            sectionIndex++
        ) {

            var section =
                sections[sectionIndex] || {};

            addSection(
                section.nodeId,
                section.nodeName,
                section.previousMeetingId,
                section.previousMeetingDate
            );
        }

        var participants =
            Array.isArray(
                meeting.participants
            )
                ? meeting.participants
                : [];

        for (
            var participantIndex = 0;
            participantIndex < participants.length;
            participantIndex++
        ) {

            var participant =
                participants[participantIndex];

            if (!participant) {
                continue;
            }

            var participantSectionId =
                getParticipantMeetingSectionIdLocal(
                    participant,
                    meeting
                );

            participant.sectionNodeId =
                participantSectionId;

            participant.sectionNodeName =
                getMeetingSectionNameLocal(
                    participantSectionId,
                    participant.sectionNodeName || ""
                );

            addSection(
                participantSectionId,
                participant.sectionNodeName,
                participant.previousMeetingId,
                participant.previousMeetingDate
            );
        }

        meeting.kvcSections =
            normalizedSections;

        return normalizedSections;
    }


    function meetingContainsSectionLocal(
        meeting,
        sectionNodeId
    ) {

        if (!meeting || !sectionNodeId) {
            return false;
        }

        var sections =
            normalizeMeetingSectionsLocal(
                meeting
            );

        for (
            var i = 0;
            i < sections.length;
            i++
        ) {
            if (
                String(sections[i].nodeId) ===
                String(sectionNodeId)
            ) {
                return true;
            }
        }

        return false;
    }


    function getImmediateChildMeetingKvcNodes(
        node
    ) {

        var result = [];

        function walk(currentNode) {

            var children =
                currentNode &&
                Array.isArray(currentNode.children)
                    ? currentNode.children
                    : [];

            for (
                var i = 0;
                i < children.length;
                i++
            ) {

                var child =
                    children[i];

                if (!child) {
                    continue;
                }

                /* В выбор попадают все дочерние КВЦ, но не ОП. */
                if (
                    child.type !== "op"
                ) {
                    result.push(
                        child
                    );
                }

                /* Идём глубже, чтобы можно было выбрать КВЦ любого уровня ветки. */
                walk(
                    child
                );
            }
        }

        walk(
            node
        );

        return result;
    }


    function createMeetingParticipantsForSection(
        sectionNode
    ) {

        var users =
            collectMeetingParticipants(
                sectionNode
            );

        var participants = [];

        var rootMeetingOwnerId =
            treeData &&
            currentMeetingsNode &&
            String(currentMeetingsNode.id) ===
                String(treeData.id) &&
            currentMeetingsNode.ownerId !== null &&
            currentMeetingsNode.ownerId !== undefined
                ? String(currentMeetingsNode.ownerId).trim()
                : "";

        var rootMeetingOwnerName =
            treeData &&
            currentMeetingsNode &&
            String(currentMeetingsNode.id) ===
                String(treeData.id) &&
            currentMeetingsNode.owner
                ? String(currentMeetingsNode.owner)
                    .trim()
                    .toLowerCase()
                : "";

        for (
            var i = 0;
            i < users.length;
            i++
        ) {

            var user =
                users[i];

            /*
             * Если собрание создано на самом первом корневом КВЦ,
             * его владелец не включается ни в одну секцию этого собрания.
             */
            if (
                (
                    rootMeetingOwnerId &&
                    user.id &&
                    String(user.id).trim() ===
                        rootMeetingOwnerId
                ) ||
                (
                    rootMeetingOwnerName &&
                    user.name &&
                    String(user.name)
                        .trim()
                        .toLowerCase() ===
                        rootMeetingOwnerName
                )
            ) {
                continue;
            }

            participants.push({
                sectionNodeId:
                    String(sectionNode.id),
                sectionNodeName:
                    getMeetingNodeName(
                        sectionNode
                    ),
                userId:
                    user.id,
                userName:
                    user.name,
                previousObligation:
                    "",
                previousResult:
                    "",
                previousStatus:
                    "",
                previousComment:
                    "",
                currentObligation:
                    "",
                currentResult:
                    "",
                attendance:
                    "",
                currentComment:
                    ""
            });
        }

        return participants;
    }


    function getSelectedMeetingSectionIdsLocal() {

        if (!currentMeeting) {
            return [];
        }

        var sections =
            normalizeMeetingSectionsLocal(
                currentMeeting
            );

        var result = [];

        for (
            var i = 0;
            i < sections.length;
            i++
        ) {
            result.push(
                String(sections[i].nodeId)
            );
        }

        return result;
    }


    function closeMeetingScopeDropdown() {

        if (!meetingScopeDropdown) {
            return;
        }

        meetingScopeDropdown.classList.remove(
            "open"
        );

        meetingScopeSelection.classList.remove(
            "open"
        );
    }


    function renderMeetingScopeTags() {

        if (!meetingScopeTags) {
            return;
        }

        meetingScopeTags.innerHTML =
            "";

        if (!currentMeeting) {
            return;
        }

        var sections =
            normalizeMeetingSectionsLocal(
                currentMeeting
            );

        var mainSectionId =
            getMeetingMainSectionNodeIdLocal(
                currentMeeting
            );

        for (
            var i = 0;
            i < sections.length;
            i++
        ) {

            (function (section) {

                var tag =
                    document.createElement(
                        "div"
                    );

                tag.className =
                    "kvc-meeting-scope-tag" +
                    (
                        String(section.nodeId) ===
                        String(mainSectionId)
                            ? " main"
                            : ""
                    );

                var name =
                    document.createElement(
                        "span"
                    );

                name.className =
                    "kvc-meeting-scope-tag-name";

                name.textContent =
                    section.nodeName ||
                    "КВЦ";

                tag.appendChild(
                    name
                );

                if (
                    String(section.nodeId) !==
                    String(mainSectionId)
                ) {

                    var remove =
                        document.createElement(
                            "button"
                        );

                    remove.type =
                        "button";

                    remove.className =
                        "kvc-meeting-scope-tag-remove";

                    remove.textContent =
                        "×";

                    remove.title =
                        "Убрать КВЦ из собрания";

                    remove.onclick =
                        function (event) {

                            event.preventDefault();
                            event.stopPropagation();

                            setMeetingSectionSelectedLocal(
                                section.nodeId,
                                false
                            );
                        };

                    tag.appendChild(
                        remove
                    );
                }

                meetingScopeTags.appendChild(
                    tag
                );

            })(sections[i]);
        }
    }


    function renderMeetingScopeOptions(
        searchText
    ) {

        if (!meetingScopeOptions) {
            return;
        }

        meetingScopeOptions.innerHTML =
            "";

        var children =
            getImmediateChildMeetingKvcNodes(
                currentMeetingsNode
            );

        var selectedIds =
            getSelectedMeetingSectionIdsLocal();

        var selectedMap = {};

        for (
            var selectedIndex = 0;
            selectedIndex < selectedIds.length;
            selectedIndex++
        ) {
            selectedMap[
                String(selectedIds[selectedIndex])
            ] = true;
        }

        var query =
            String(
                searchText || ""
            )
                .trim()
                .toLowerCase();

        var visibleCount = 0;

        for (
            var i = 0;
            i < children.length;
            i++
        ) {

            (function (child) {

                var childName =
                    getMeetingNodeName(
                        child
                    );

                if (
                    query &&
                    String(childName)
                        .toLowerCase()
                        .indexOf(query) === -1
                ) {
                    return;
                }

                visibleCount++;

                var option =
                    document.createElement(
                        "label"
                    );

                option.className =
                    "kvc-meeting-scope-option";

                var checkbox =
                    document.createElement(
                        "input"
                    );

                checkbox.type =
                    "checkbox";

                checkbox.checked =
                    !!selectedMap[
                        String(child.id)
                    ];

                var text =
                    document.createElement(
                        "span"
                    );

                text.textContent =
                    childName;

                checkbox.onchange =
                    function () {

                        setMeetingSectionSelectedLocal(
                            child.id,
                            checkbox.checked
                        );
                    };

                option.appendChild(
                    checkbox
                );

                option.appendChild(
                    text
                );

                meetingScopeOptions.appendChild(
                    option
                );

            })(children[i]);
        }

        if (!visibleCount) {

            var empty =
                document.createElement(
                    "div"
                );

            empty.className =
                "kvc-meeting-scope-empty";

            empty.textContent =
                children.length
                    ? "Ничего не найдено"
                    : "У этого КВЦ нет непосредственных дочерних КВЦ";

            meetingScopeOptions.appendChild(
                empty
            );
        }
    }


    function setMeetingSectionSelectedLocal(
        sectionNodeId,
        selected
    ) {

        if (
            !currentMeeting ||
            !currentMeetingIsNew
        ) {
            return;
        }

        var mainSectionId =
            getMeetingMainSectionNodeIdLocal(
                currentMeeting
            );

        var desired = {};
        desired[mainSectionId] = true;

        var existingSections =
            normalizeMeetingSectionsLocal(
                currentMeeting
            );

        for (
            var i = 0;
            i < existingSections.length;
            i++
        ) {
            desired[
                String(existingSections[i].nodeId)
            ] = true;
        }

        var targetId =
            String(sectionNodeId || "");

        if (
            targetId &&
            targetId !== mainSectionId
        ) {
            if (selected) {
                desired[targetId] = true;
            } else {
                delete desired[targetId];
            }
        }

        var sectionOrder = [];
        sectionOrder.push(mainSectionId);

        var childNodes =
            getImmediateChildMeetingKvcNodes(
                currentMeetingsNode
            );

        for (
            var childIndex = 0;
            childIndex < childNodes.length;
            childIndex++
        ) {
            var childId =
                String(childNodes[childIndex].id);

            if (desired[childId]) {
                sectionOrder.push(childId);
            }
        }

        syncNewMeetingSectionsLocal(
            sectionOrder
        );
    }


    function syncNewMeetingSectionsLocal(
        sectionIds
    ) {

        if (
            !currentMeeting ||
            !currentMeetingsNode
        ) {
            return;
        }

        normalizeMeetingSectionsLocal(
            currentMeeting
        );

        var oldParticipants =
            Array.isArray(
                currentMeeting.participants
            )
                ? currentMeeting.participants
                : [];

        var oldBySection = {};

        for (
            var i = 0;
            i < oldParticipants.length;
            i++
        ) {

            var oldSectionId =
                getParticipantMeetingSectionIdLocal(
                    oldParticipants[i],
                    currentMeeting
                );

            if (!oldBySection[oldSectionId]) {
                oldBySection[oldSectionId] = [];
            }

            oldBySection[oldSectionId].push(
                oldParticipants[i]
            );
        }

        var newSections = [];
        var newParticipants = [];
        var seen = {};

        for (
            var sectionIndex = 0;
            sectionIndex < sectionIds.length;
            sectionIndex++
        ) {

            var id =
                String(sectionIds[sectionIndex] || "");

            if (!id || seen[id]) {
                continue;
            }

            seen[id] = true;

            var node =
                getMeetingSectionNodeLocal(
                    id
                );

            if (!node) {
                continue;
            }

            newSections.push({
                nodeId:
                    id,
                nodeName:
                    getMeetingNodeName(
                        node
                    ),
                previousMeetingId:
                    null,
                previousMeetingDate:
                    ""
            });

            var sectionParticipants =
                oldBySection[id] &&
                oldBySection[id].length
                    ? oldBySection[id]
                    : createMeetingParticipantsForSection(
                        node
                    );

            for (
                var participantIndex = 0;
                participantIndex < sectionParticipants.length;
                participantIndex++
            ) {

                sectionParticipants[participantIndex].sectionNodeId =
                    id;

                sectionParticipants[participantIndex].sectionNodeName =
                    getMeetingNodeName(
                        node
                    );

                newParticipants.push(
                    sectionParticipants[participantIndex]
                );
            }
        }

        currentMeeting.kvcSections =
            newSections;

        currentMeeting.participants =
            newParticipants;

        applyPreviousMeetingData(
            currentMeeting,
            currentMeeting.dateTime,
            true
        );

        currentMeetingEditParticipantSnapshot =
            createMeetingParticipantEditSnapshot(
                currentMeeting
            );

        renderMeetingScopeTags();

        renderMeetingScopeOptions(
            meetingScopeSearch
                ? meetingScopeSearch.value
                : ""
        );

        renderMeetingParticipants();
    }



    function getMeetingParticipantKey(
        userId,
        userName
    ) {

        if (
            userId !== null &&
            userId !== undefined &&
            String(userId).trim()
        ) {

            return (
                "id:" +
                String(userId).trim()
            );
        }

        return (
            "name:" +
            String(
                userName || ""
            )
                .trim()
                .toLowerCase()
        );
    }

    function findMeetingParticipant(
        meeting,
        userId,
        userName,
        sectionNodeId
    ) {

        if (
            !meeting ||
            !Array.isArray(
                meeting.participants
            )
        ) {
            return null;
        }

        normalizeMeetingSectionsLocal(
            meeting
        );

        var key =
            getMeetingParticipantKey(
                userId,
                userName
            );

        var targetSectionId =
            sectionNodeId !== null &&
            sectionNodeId !== undefined &&
            String(sectionNodeId)
                ? String(sectionNodeId)
                : getMeetingMainSectionNodeIdLocal(
                    meeting
                );

        for (
            var i = 0;
            i < meeting.participants.length;
            i++
        ) {

            var item =
                meeting.participants[i];

            if (
                String(
                    getParticipantMeetingSectionIdLocal(
                        item,
                        meeting
                    )
                ) !== targetSectionId
            ) {
                continue;
            }

            if (
                getMeetingParticipantKey(
                    item.userId,
                    item.userName
                ) === key
            ) {
                return item;
            }
        }

        return null;
    }



    function findPreviousMeeting(
        dateTime,
        excludeMeetingId
    ) {

        var targetTime =
            new Date(
                dateTime
            ).getTime();

        var previous =
            null;

        var previousTime =
            -Infinity;

        for (
            var i = 0;
            i < currentMeetings.length;
            i++
        ) {

            var meeting =
                currentMeetings[i];

            if (
                excludeMeetingId &&
                String(meeting.id) ===
                    String(excludeMeetingId)
            ) {
                continue;
            }

            var meetingTime =
                new Date(
                    meeting.dateTime
                ).getTime();

            if (
                isNaN(meetingTime)
            ) {
                continue;
            }

            if (
                meetingTime < targetTime &&
                meetingTime > previousTime
            ) {

                previous =
                    meeting;

                previousTime =
                    meetingTime;
            }
        }

        return previous;
    }

    function findPreviousMeetingForSectionLocal(
        dateTime,
        excludeMeetingId,
        sectionNodeId
    ) {

        var targetTime =
            new Date(
                dateTime
            ).getTime();

        var previous = null;
        var previousTime = -Infinity;

        for (
            var i = 0;
            i < currentMeetings.length;
            i++
        ) {

            var meeting =
                currentMeetings[i];

            if (
                !meeting ||
                (
                    excludeMeetingId &&
                    String(meeting.id) ===
                        String(excludeMeetingId)
                ) ||
                !meetingContainsSectionLocal(
                    meeting,
                    sectionNodeId
                )
            ) {
                continue;
            }

            var meetingTime =
                new Date(
                    meeting.dateTime
                ).getTime();

            if (
                isNaN(meetingTime)
            ) {
                continue;
            }

            if (
                meetingTime < targetTime &&
                meetingTime > previousTime
            ) {
                previous = meeting;
                previousTime = meetingTime;
            }
        }

        return previous;
    }

    function applyPreviousMeetingData(
        meeting,
        dateTime,
        resetPreviousEvaluation
    ) {

        if (!meeting) {
            return;
        }

        var sections =
            normalizeMeetingSectionsLocal(
                meeting
            );

        var mainSectionId =
            getMeetingMainSectionNodeIdLocal(
                meeting
            );

        for (
            var sectionIndex = 0;
            sectionIndex < sections.length;
            sectionIndex++
        ) {

            var section =
                sections[sectionIndex];

            var previousMeeting =
                findPreviousMeetingForSectionLocal(
                    dateTime,
                    meeting.id,
                    section.nodeId
                );

            section.previousMeetingId =
                previousMeeting
                    ? previousMeeting.id
                    : null;

            section.previousMeetingDate =
                previousMeeting
                    ? previousMeeting.dateTime
                    : "";

            if (
                String(section.nodeId) ===
                String(mainSectionId)
            ) {
                meeting.previousMeetingId =
                    section.previousMeetingId;

                meeting.previousMeetingDate =
                    section.previousMeetingDate;
            }
        }

        var participants =
            Array.isArray(
                meeting.participants
            )
                ? meeting.participants
                : [];

        for (
            var i = 0;
            i < participants.length;
            i++
        ) {

            var participant =
                participants[i];

            var sectionNodeId =
                getParticipantMeetingSectionIdLocal(
                    participant,
                    meeting
                );

            var previousMeeting =
                findPreviousMeetingForSectionLocal(
                    dateTime,
                    meeting.id,
                    sectionNodeId
                );

            var previousParticipant =
                findMeetingParticipant(
                    previousMeeting,
                    participant.userId,
                    participant.userName,
                    sectionNodeId
                );

            participant.previousMeetingId =
                previousMeeting
                    ? previousMeeting.id
                    : null;

            participant.previousMeetingDate =
                previousMeeting
                    ? previousMeeting.dateTime
                    : "";

            participant.previousObligation =
                previousParticipant
                    ? previousParticipant.currentObligation || ""
                    : "";

            participant.previousResult =
                previousParticipant
                    ? previousParticipant.currentResult || ""
                    : "";

            participant.previousComment =
                previousParticipant
                    ? previousParticipant.currentComment || ""
                    : "";

            if (
                resetPreviousEvaluation
            ) {
                participant.previousStatus =
                    "";
            }
        }
    }

    function buildNewMeeting(node) {

        var dateTime =
            getNowDateTimeLocal();

        var mainSectionId =
            String(node.id);

        var meeting =
            {
                id:
                    "meeting-" +
                    Date.now() +
                    "-" +
                    Math.floor(
                        Math.random() *
                        100000
                    ),
                kvcId:
                    mainSectionId,
                dateTime:
                    dateTime,
                previousMeetingId:
                    null,
                previousMeetingDate:
                    "",
                summary:
                    "",
                closed:
                    false,
                kvcSections:
                    [
                        {
                            nodeId:
                                mainSectionId,
                            nodeName:
                                getMeetingNodeName(
                                    node
                                ),
                            previousMeetingId:
                                null,
                            previousMeetingDate:
                                ""
                        }
                    ],
                participants:
                    createMeetingParticipantsForSection(
                        node
                    )
            };

        applyPreviousMeetingData(
            meeting,
            dateTime,
            true
        );

        return meeting;
    }



    function countMeetingValues(
        meeting,
        field,
        value
    ) {

        var participants =
            meeting &&
            Array.isArray(
                meeting.participants
            )
                ? meeting.participants
                : [];

        var count = 0;

        for (
            var i = 0;
            i < participants.length;
            i++
        ) {

            if (
                participants[i][field] ===
                value
            ) {
                count++;
            }
        }

        return count;
    }


    function showMeetingListView() {

        meetingsListView.classList.add(
            "visible"
        );

        meetingDetailView.classList.remove(
            "visible"
        );

        meetingListFooter.classList.add(
            "visible"
        );

        meetingDetailFooter.classList.remove(
            "visible"
        );

        meetingCreate.style.display =
            currentUserCanManageMeetings
                ? "inline-flex"
                : "none";

        meetingTitle.textContent =
            "Собрания";

        meetingSubtitle.textContent =
            getMeetingNodeName(
                currentMeetingsNode
            );

        currentMeeting =
            null;

        currentMeetingIsNew =
            false;

        currentMeetingIsEditing =
            false;
    }


    function renderMeetingsList() {

        meetingsTableBody.innerHTML =
            "";

        if (
            !currentMeetings.length
        ) {

            var emptyRow =
                document.createElement(
                    "tr"
                );

            var emptyCell =
                document.createElement(
                    "td"
                );

            emptyCell.colSpan =
                6;

            emptyCell.className =
                "kvc-meeting-empty";

            emptyCell.textContent =
                "По этой КВЦ / ОП собраний пока нет";

            emptyRow.appendChild(
                emptyCell
            );

            meetingsTableBody.appendChild(
                emptyRow
            );

            return;
        }

        var sorted =
            currentMeetings
                .slice()
                .sort(
                    function (
                        a,
                        b
                    ) {

                        return (
                            new Date(
                                b.dateTime
                            ).getTime() -
                            new Date(
                                a.dateTime
                            ).getTime()
                        );
                    }
                );

        for (
            var i = 0;
            i < sorted.length;
            i++
        ) {

            var meeting =
                sorted[i];

            var row =
                document.createElement(
                    "tr"
                );

            row.className =
                meeting.closed
                    ? "kvc-meeting-row-closed"
                    : "kvc-meeting-row-open";

            var dateCell =
                document.createElement(
                    "td"
                );

            var dateButton =
                document.createElement(
                    "button"
                );

            dateButton.type =
                "button";

            dateButton.className =
                "kvc-meeting-date-link";

            dateButton.textContent =
                formatMeetingDateTime(
                    meeting.dateTime
                );

            dateButton.onclick =
                (function (
                    selectedMeeting
                ) {

                    return function () {

                        openMeetingDetail(
                            selectedMeeting,
                            false
                        );
                    };

                })(meeting);

            var dateWrap =
                document.createElement(
                    "div"
                );

            dateWrap.className =
                "kvc-meeting-date-wrap";

            dateWrap.appendChild(
                dateButton
            );

            var statusBadge =
                document.createElement(
                    "span"
                );

            statusBadge.className =
                "kvc-meeting-status-badge " +
                (
                    meeting.closed
                        ? "closed"
                        : "open"
                );

            statusBadge.textContent =
                meeting.closed
                    ? "Закрыто"
                    : "Открыто";

            dateWrap.appendChild(
                statusBadge
            );

            dateCell.appendChild(
                dateWrap
            );

            row.appendChild(
                dateCell
            );

            /*
             * Итоговые цифры в списке показываем только после
             * закрытия собрания. Пока собрание открыто, дата остаётся
             * доступной для перехода в него, а итоговые ячейки пустые.
             */
            var values = meeting.closed
                ? [

                    countMeetingValues(
                        meeting,
                        "attendance",
                        "present"
                    ),

                    countMeetingValues(
                        meeting,
                        "attendance",
                        "absent"
                    ) +
                    countMeetingValues(
                        meeting,
                        "attendance",
                        "absent_excused"
                    ) +
                    countMeetingValues(
                        meeting,
                        "attendance",
                        "absent_unexcused"
                    ),

                    countMeetingValues(
                        meeting,
                        "previousStatus",
                        "done"
                    ),

                    countMeetingValues(
                        meeting,
                        "previousStatus",
                        "partial"
                    ),

                    countMeetingValues(
                        meeting,
                        "previousStatus",
                        "not_done"
                    )
                ]
                : ["", "", "", "", ""];

            for (
                var valueIndex = 0;
                valueIndex < values.length;
                valueIndex++
            ) {

                var cell =
                    document.createElement(
                        "td"
                    );

                cell.textContent =
                    values[valueIndex];

                row.appendChild(
                    cell
                );
            }

            meetingsTableBody.appendChild(
                row
            );
        }
    }


    function getPreviousStatusClass(status) {

        if (
            status === "done"
        ) {
            return "kvc-meeting-prev-done";
        }

        if (
            status === "partial"
        ) {
            return "kvc-meeting-prev-partial";
        }

        if (
            status === "not_done"
        ) {
            return "kvc-meeting-prev-not-done";
        }

        return "";
    }


    function buildMeetingTextArea(
        value,
        readonly
    ) {

        var textarea =
            document.createElement(
                "textarea"
            );

        textarea.className =
            "kvc-meeting-cell-textarea";

        textarea.value =
            value || "";

        if (readonly) {
            textarea.readOnly =
                true;
        }

        return textarea;
    }


    function buildPreviousStatusSelect(value) {

        var select =
            document.createElement(
                "select"
            );

        select.className =
            "kvc-meeting-cell-select";

        select.innerHTML =
            '<option value="">—</option>' +
            '<option value="done">Выполнено</option>' +
            '<option value="partial">Выполнено частично</option>' +
            '<option value="not_done">Не выполнено</option>';

        select.value =
            value || "";

        return select;
    }


    function buildAttendanceSelect(value) {

        var select =
            document.createElement(
                "select"
            );

        select.className =
            "kvc-meeting-cell-select";

        select.innerHTML =
            '<option value="">----</option>' +
            '<option value="present">Присутствовал</option>' +
            '<option value="absent_excused">Отсутствие по уважительным причинам</option>' +
            '<option value="absent_unexcused">Отсутствие по НЕ уважительным причинам</option>';

        /* Совместимость с уже сохранённым старым значением absent. */
        if (
            value === "absent"
        ) {
            select.value =
                "absent_unexcused";
        } else if (
            value === "present" ||
            value === "absent_excused" ||
            value === "absent_unexcused"
        ) {
            select.value =
                value;
        } else {
            select.value =
                "";
        }

        return select;
    }


    function isCurrentUserMeetingParticipant(
        participant
    ) {

        if (
            !participant ||
            !currentUserData
        ) {
            return false;
        }

        if (
            participant.userId &&
            currentUserData.id &&
            String(participant.userId) ===
                String(currentUserData.id)
        ) {
            return true;
        }

        var currentUserName =
            currentUserData.fio ||
            currentUserData.name ||
            "";

        return (
            !!participant.userName &&
            !!currentUserName &&
            String(participant.userName).trim() ===
                String(currentUserName).trim()
        );
    }

    function findNextMeetingLocal(
        meeting,
        sectionNodeId
    ) {

        if (
            !meeting ||
            !Array.isArray(
                currentMeetings
            )
        ) {
            return null;
        }

        var targetSectionId =
            sectionNodeId ||
            getMeetingMainSectionNodeIdLocal(
                meeting
            );

        var currentTime =
            meeting.dateTime
                ? new Date(
                    meeting.dateTime
                ).getTime()
                : 0;

        var next = null;
        var nextTime = Infinity;

        for (
            var i = 0;
            i < currentMeetings.length;
            i++
        ) {

            var candidate =
                currentMeetings[i];

            if (
                !candidate ||
                String(candidate.id) ===
                    String(meeting.id) ||
                !meetingContainsSectionLocal(
                    candidate,
                    targetSectionId
                )
            ) {
                continue;
            }

            var candidateTime =
                candidate.dateTime
                    ? new Date(
                        candidate.dateTime
                    ).getTime()
                    : 0;

            if (
                candidateTime > currentTime &&
                candidateTime < nextTime
            ) {
                next = candidate;
                nextTime = candidateTime;
            }
        }

        return next;
    }

    function isMeetingOutcomeLockedLocal(
        meeting,
        sectionNodeId
    ) {

        var nextMeeting =
            findNextMeetingLocal(
                meeting,
                sectionNodeId
            );

        return !!(
            nextMeeting &&
            nextMeeting.closed
        );
    }

    function closePersonalMeetingEdit() {

        meetingPersonalOverlay.classList.remove(
            "open"
        );

        currentPersonalMeetingUserId =
            null;

        currentPersonalMeetingUserName =
            "";

        currentPersonalMeetingSectionNodeId =
            "";
    }

    function openPersonalMeetingEdit(
        participant
    ) {

        if (
            !currentMeeting ||
            currentMeetingIsNew ||
            currentMeetingIsEditing ||
            !isCurrentUserMeetingParticipant(
                participant
            )
        ) {
            return;
        }

        var sectionNodeId =
            getParticipantMeetingSectionIdLocal(
                participant,
                currentMeeting
            );

        var outcomeLocked =
            isMeetingOutcomeLockedLocal(
                currentMeeting,
                sectionNodeId
            );

        var obligationEditable =
            !currentMeeting.closed;

        if (
            !obligationEditable &&
            outcomeLocked
        ) {
            return;
        }

        currentPersonalMeetingUserId =
            participant.userId || "";

        currentPersonalMeetingUserName =
            participant.userName || "";

        currentPersonalMeetingSectionNodeId =
            sectionNodeId;

        meetingPersonalSubtitle.textContent =
            (participant.userName || "") +
            " · " +
            getMeetingSectionNameLocal(
                sectionNodeId,
                participant.sectionNodeName || ""
            ) +
            " · " +
            formatMeetingDateTime(
                currentMeeting.dateTime
            );

        meetingPersonalObligation.value =
            participant.currentObligation || "";

        meetingPersonalResult.value =
            participant.currentResult || "";

        meetingPersonalComment.value =
            participant.currentComment || "";

        meetingPersonalObligation.readOnly =
            !obligationEditable;

        meetingPersonalResult.readOnly =
            outcomeLocked;

        meetingPersonalComment.readOnly =
            outcomeLocked;

        meetingPersonalOverlay.classList.add(
            "open"
        );

        setTimeout(
            function () {

                if (obligationEditable) {
                    meetingPersonalObligation.focus();
                } else if (!outcomeLocked) {
                    meetingPersonalResult.focus();
                }
            },
            0
        );
    }

    async function savePersonalMeetingEdit() {

        if (
            !currentMeetingsNode ||
            !currentMeeting ||
            currentPersonalMeetingUserId === null
        ) {
            return;
        }

        meetingPersonalSave.disabled =
            true;

        meetingPersonalSave.textContent =
            "Сохранение...";

        try {

            var personalUpdateData =
                {
                    currentObligation:
                        meetingPersonalObligation.value || ""
                };

            if (
                !isMeetingOutcomeLockedLocal(
                    currentMeeting,
                    currentPersonalMeetingSectionNodeId
                )
            ) {
                personalUpdateData.currentResult =
                    meetingPersonalResult.value || "";

                personalUpdateData.currentComment =
                    meetingPersonalComment.value || "";
            }

            var currentMeetingIdBeforeReload =
                String(currentMeeting.id);

            await window.KvcApi.updateKVCMeetingParticipant(
                String(
                    currentMeetingsNode.id
                ),
                String(
                    currentMeeting.id
                ),
                String(
                    currentPersonalMeetingUserId || ""
                ),
                String(
                    currentPersonalMeetingUserName || ""
                ),
                personalUpdateData,
                String(
                    currentPersonalMeetingSectionNodeId || ""
                )
            );

            var loadedMeetings =
                await window.KvcApi.getKVCMeetings(
                    String(
                        currentMeetingsNode.id
                    )
                );

            currentMeetings =
                Array.isArray(
                    loadedMeetings
                )
                    ? loadedMeetings
                    : [];

            for (
                var i = 0;
                i < currentMeetings.length;
                i++
            ) {
                if (
                    String(currentMeetings[i].id) ===
                    currentMeetingIdBeforeReload
                ) {
                    currentMeeting =
                        cloneLocalJson(
                            currentMeetings[i]
                        );
                    break;
                }
            }

            closePersonalMeetingEdit();

            renderMeetingParticipants();

        } catch (error) {

            console.error(
                "Ошибка сохранения личных обязательств собрания",
                error
            );

            alert(
                error && error.message
                    ? error.message
                    : "Не удалось сохранить обязательства"
            );

        } finally {

            meetingPersonalSave.disabled =
                false;

            meetingPersonalSave.textContent =
                "Сохранить";
        }
    }

    async function deleteMeetingParticipantFromCurrentMeeting(
        participant,
        deleteButton
    ) {

        if (
            !isAdmin ||
            !currentMeetingsNode ||
            !currentMeeting ||
            currentMeetingIsNew ||
            !participant
        ) {
            return;
        }

        var participantName =
            participant.userName ||
            "пользователя";

        var sectionNodeId =
            getParticipantMeetingSectionIdLocal(
                participant,
                currentMeeting
            );

        var sectionName =
            getMeetingSectionNameLocal(
                sectionNodeId,
                participant.sectionNodeName || ""
            );

        if (
            !window.confirm(
                "Удалить «" +
                participantName +
                "» из таблицы обязательств КВЦ «" +
                sectionName +
                "»?"
            )
        ) {
            return;
        }

        if (deleteButton) {
            deleteButton.disabled = true;
        }

        try {

            await window.KvcApi.deleteKVCMeetingParticipant(
                String(
                    currentMeetingsNode.id
                ),
                String(
                    currentMeeting.id
                ),
                String(
                    participant.userId || ""
                ),
                String(
                    participant.userName || ""
                ),
                String(
                    sectionNodeId || ""
                )
            );

            var loadedMeetings =
                await window.KvcApi.getKVCMeetings(
                    String(
                        currentMeetingsNode.id
                    )
                );

            currentMeetings =
                Array.isArray(
                    loadedMeetings
                )
                    ? loadedMeetings
                    : [];

            var refreshedMeeting = null;

            for (
                var i = 0;
                i < currentMeetings.length;
                i++
            ) {
                if (
                    String(currentMeetings[i].id) ===
                    String(currentMeeting.id)
                ) {
                    refreshedMeeting =
                        currentMeetings[i];
                    break;
                }
            }

            renderMeetingsList();

            if (refreshedMeeting) {
                currentMeeting =
                    cloneLocalJson(
                        refreshedMeeting
                    );
                currentMeetingIsEditing = false;
                currentMeetingEditParticipantSnapshot = null;
                updateMeetingDetailMode();
            } else {
                showMeetingListView();
            }

        } catch (error) {

            console.error(
                "Ошибка удаления участника из собрания",
                error
            );

            alert(
                error && error.message
                    ? error.message
                    : "Не удалось удалить участника из собрания"
            );

            if (deleteButton) {
                deleteButton.disabled = false;
            }
        }
    }

    function renderMeetingParticipants() {

        if (!meetingSectionsContainer) {
            return;
        }

        meetingSectionsContainer.innerHTML =
            "";

        if (
            !currentMeeting ||
            !Array.isArray(
                currentMeeting.participants
            )
        ) {
            return;
        }

        var sections =
            normalizeMeetingSectionsLocal(
                currentMeeting
            );

        var editable =
            currentUserCanManageMeetings &&
            (
                currentMeetingIsNew ||
                currentMeetingIsEditing
            );

        for (
            var sectionIndex = 0;
            sectionIndex < sections.length;
            sectionIndex++
        ) {

            (function (section) {

                var sectionWrap =
                    document.createElement(
                        "div"
                    );

                sectionWrap.className =
                    "kvc-meeting-section";

                var sectionTitle =
                    document.createElement(
                        "div"
                    );

                sectionTitle.className =
                    "kvc-meeting-section-title";

                sectionTitle.textContent =
                    section.nodeName ||
                    getMeetingSectionNameLocal(
                        section.nodeId,
                        "КВЦ"
                    );

                sectionWrap.appendChild(
                    sectionTitle
                );

                var tableBox =
                    document.createElement(
                        "div"
                    );

                tableBox.className =
                    "kvc-meeting-detail-table-box";

                var table =
                    document.createElement(
                        "table"
                    );

                table.className =
                    "kvc-meeting-detail-table";

                var thead =
                    document.createElement(
                        "thead"
                    );

                var firstHeaderRow =
                    document.createElement(
                        "tr"
                    );

                var userHeader =
                    document.createElement(
                        "th"
                    );

                userHeader.rowSpan = 2;
                userHeader.className =
                    "kvc-meeting-user-cell";
                userHeader.textContent =
                    "Пользователь";

                firstHeaderRow.appendChild(
                    userHeader
                );

                var previousHeader =
                    document.createElement(
                        "th"
                    );

                previousHeader.colSpan = 4;
                previousHeader.textContent =
                    section.previousMeetingDate
                        ? "Прошлое собрание (" +
                            formatMeetingDateOnly(
                                section.previousMeetingDate
                            ) +
                            ")"
                        : "Прошлое собрание";

                firstHeaderRow.appendChild(
                    previousHeader
                );

                var currentHeader =
                    document.createElement(
                        "th"
                    );

                currentHeader.colSpan = 3;
                currentHeader.textContent =
                    "Текущее собрание (" +
                    formatMeetingDateOnly(
                        currentMeeting.dateTime
                    ) +
                    ")";

                firstHeaderRow.appendChild(
                    currentHeader
                );

                thead.appendChild(
                    firstHeaderRow
                );

                var secondHeaderRow =
                    document.createElement(
                        "tr"
                    );

                var headerDefinitions = [
                    ["Обязательство", "kvc-meeting-prev-obligation"],
                    ["Итог", "kvc-meeting-prev-result"],
                    ["Статус", "kvc-meeting-prev-status"],
                    ["Комментарий", "kvc-meeting-prev-comment"],
                    ["Обязательство", "kvc-meeting-current-obligation"],
                    ["Состояние", "kvc-meeting-current-state"],
                    ["Комментарий", "kvc-meeting-current-comment"]
                ];

                for (
                    var headerIndex = 0;
                    headerIndex < headerDefinitions.length;
                    headerIndex++
                ) {
                    var th =
                        document.createElement(
                            "th"
                        );
                    th.className =
                        headerDefinitions[headerIndex][1];
                    th.textContent =
                        headerDefinitions[headerIndex][0];
                    secondHeaderRow.appendChild(
                        th
                    );
                }

                thead.appendChild(
                    secondHeaderRow
                );

                table.appendChild(
                    thead
                );

                var tbody =
                    document.createElement(
                        "tbody"
                    );

                var rowsCount = 0;

                for (
                    var i = 0;
                    i < currentMeeting.participants.length;
                    i++
                ) {

                    var participant =
                        currentMeeting.participants[i];

                    if (
                        String(
                            getParticipantMeetingSectionIdLocal(
                                participant,
                                currentMeeting
                            )
                        ) !== String(section.nodeId)
                    ) {
                        continue;
                    }

                    rowsCount++;

                    (function (
                        participant,
                        participantIndex
                    ) {

                        var row =
                            document.createElement(
                                "tr"
                            );

                        var userCell =
                            document.createElement(
                                "td"
                            );

                        userCell.className =
                            "kvc-meeting-user-cell";

                        var userNameWrap =
                            document.createElement(
                                "div"
                            );

                        userNameWrap.className =
                            "kvc-meeting-user-name-wrap";

                        var userNameText =
                            document.createElement(
                                "span"
                            );

                        userNameText.className =
                            "kvc-meeting-user-name-text";

                        userNameText.textContent =
                            participant.userName ||
                            "—";

                        userNameWrap.appendChild(
                            userNameText
                        );

                        var userActions =
                            document.createElement(
                                "div"
                            );

                        userActions.className =
                            "kvc-meeting-user-actions";

                        if (
                            !currentMeetingIsNew &&
                            !currentMeetingIsEditing &&
                            isCurrentUserMeetingParticipant(
                                participant
                            ) &&
                            (
                                !currentMeeting.closed ||
                                !isMeetingOutcomeLockedLocal(
                                    currentMeeting,
                                    section.nodeId
                                )
                            )
                        ) {

                            var personalEditButton =
                                document.createElement(
                                    "button"
                                );

                            personalEditButton.type =
                                "button";

                            personalEditButton.className =
                                "kvc-meeting-user-edit";

                            personalEditButton.innerHTML =
                                '<svg viewBox="0 0 24 24" aria-hidden="true">' +
                                    '<path d="M4 20h4l10.5-10.5a2.12 2.12 0 0 0-3-3L5 17v3Z"></path>' +
                                    '<path d="M13.5 8.5l3 3"></path>' +
                                '</svg>';

                            personalEditButton.title =
                                "Редактировать свои обязательства";

                            personalEditButton.onclick =
                                function (event) {
                                    event.preventDefault();
                                    event.stopPropagation();
                                    openPersonalMeetingEdit(
                                        participant
                                    );
                                };

                            userActions.appendChild(
                                personalEditButton
                            );
                        }

                        if (
                            isAdmin &&
                            !currentMeetingIsNew &&
                            !currentMeetingIsEditing
                        ) {

                            var participantDeleteButton =
                                document.createElement(
                                    "button"
                                );

                            participantDeleteButton.type =
                                "button";
                            participantDeleteButton.className =
                                "kvc-meeting-user-delete";
                            participantDeleteButton.innerHTML =
                                '<svg viewBox="0 0 24 24" aria-hidden="true">' +
                                    '<path d="M4 7h16"></path>' +
                                    '<path d="M9 7V4h6v3"></path>' +
                                    '<path d="M6.5 7l1 13h9l1-13"></path>' +
                                    '<path d="M10 11v5"></path>' +
                                    '<path d="M14 11v5"></path>' +
                                '</svg>';
                            participantDeleteButton.title =
                                "Удалить пользователя из собрания";
                            participantDeleteButton.onclick =
                                function (event) {
                                    event.preventDefault();
                                    event.stopPropagation();
                                    deleteMeetingParticipantFromCurrentMeeting(
                                        participant,
                                        participantDeleteButton
                                    );
                                };

                            userActions.appendChild(
                                participantDeleteButton
                            );
                        }

                        if (
                            userActions.childNodes.length
                        ) {
                            userNameWrap.appendChild(
                                userActions
                            );
                        }

                        userCell.appendChild(
                            userNameWrap
                        );
                        row.appendChild(
                            userCell
                        );

                        var previousClass =
                            getPreviousStatusClass(
                                participant.previousStatus
                            );

                        var previousObligationCell =
                            document.createElement(
                                "td"
                            );
                        previousObligationCell.className =
                            previousClass;
                        previousObligationCell.appendChild(
                            buildMeetingTextArea(
                                participant.previousObligation,
                                true
                            )
                        );
                        row.appendChild(
                            previousObligationCell
                        );

                        var previousResultCell =
                            document.createElement(
                                "td"
                            );
                        previousResultCell.className =
                            previousClass;
                        var previousResultInput =
                            buildMeetingTextArea(
                                participant.previousResult,
                                !editable
                            );
                        if (editable) {
                            previousResultInput.oninput =
                                function () {
                                    currentMeeting.participants[
                                        participantIndex
                                    ].previousResult =
                                        previousResultInput.value;
                                };
                        }
                        previousResultCell.appendChild(
                            previousResultInput
                        );
                        row.appendChild(
                            previousResultCell
                        );

                        var previousStatusCell =
                            document.createElement(
                                "td"
                            );
                        previousStatusCell.className =
                            previousClass;
                        var previousStatusSelect =
                            buildPreviousStatusSelect(
                                participant.previousStatus
                            );
                        previousStatusSelect.disabled =
                            !editable;
                        if (editable) {
                            previousStatusSelect.onchange =
                                function () {
                                    currentMeeting.participants[
                                        participantIndex
                                    ].previousStatus =
                                        previousStatusSelect.value;
                                    renderMeetingParticipants();
                                };
                        }
                        previousStatusCell.appendChild(
                            previousStatusSelect
                        );
                        row.appendChild(
                            previousStatusCell
                        );

                        var previousCommentCell =
                            document.createElement(
                                "td"
                            );
                        previousCommentCell.className =
                            previousClass;
                        var previousCommentInput =
                            buildMeetingTextArea(
                                participant.previousComment,
                                !editable
                            );
                        if (editable) {
                            previousCommentInput.oninput =
                                function () {
                                    currentMeeting.participants[
                                        participantIndex
                                    ].previousComment =
                                        previousCommentInput.value;
                                };
                        }
                        previousCommentCell.appendChild(
                            previousCommentInput
                        );
                        row.appendChild(
                            previousCommentCell
                        );

                        var currentObligationCell =
                            document.createElement(
                                "td"
                            );
                        var currentObligationInput =
                            buildMeetingTextArea(
                                participant.currentObligation,
                                !editable
                            );
                        if (editable) {
                            currentObligationInput.oninput =
                                function () {
                                    currentMeeting.participants[
                                        participantIndex
                                    ].currentObligation =
                                        currentObligationInput.value;
                                };
                        }
                        currentObligationCell.appendChild(
                            currentObligationInput
                        );
                        row.appendChild(
                            currentObligationCell
                        );

                        var attendanceCell =
                            document.createElement(
                                "td"
                            );
                        var attendanceSelect =
                            buildAttendanceSelect(
                                participant.attendance
                            );
                        attendanceSelect.disabled =
                            !editable;
                        if (editable) {
                            attendanceSelect.onchange =
                                function () {
                                    currentMeeting.participants[
                                        participantIndex
                                    ].attendance =
                                        attendanceSelect.value;
                                };
                        }
                        attendanceCell.appendChild(
                            attendanceSelect
                        );
                        row.appendChild(
                            attendanceCell
                        );

                        var currentCommentCell =
                            document.createElement(
                                "td"
                            );
                        var currentCommentInput =
                            buildMeetingTextArea(
                                participant.currentComment,
                                !editable
                            );
                        if (editable) {
                            currentCommentInput.oninput =
                                function () {
                                    currentMeeting.participants[
                                        participantIndex
                                    ].currentComment =
                                        currentCommentInput.value;
                                };
                        }
                        currentCommentCell.appendChild(
                            currentCommentInput
                        );
                        row.appendChild(
                            currentCommentCell
                        );

                        tbody.appendChild(
                            row
                        );

                    })(participant, i);
                }

                if (!rowsCount) {
                    var emptyRow =
                        document.createElement(
                            "tr"
                        );
                    var emptyCell =
                        document.createElement(
                            "td"
                        );
                    emptyCell.colSpan = 8;
                    emptyCell.className =
                        "kvc-meeting-empty";
                    emptyCell.textContent =
                        "В этом КВЦ нет участников для собрания";
                    emptyRow.appendChild(
                        emptyCell
                    );
                    tbody.appendChild(
                        emptyRow
                    );
                }

                table.appendChild(
                    tbody
                );
                tableBox.appendChild(
                    table
                );
                sectionWrap.appendChild(
                    tableBox
                );
                meetingSectionsContainer.appendChild(
                    sectionWrap
                );

            })(sections[sectionIndex]);
        }
    }

    function updateMeetingDetailMode() {

        if (!currentMeeting) {
            return;
        }

        normalizeMeetingSectionsLocal(
            currentMeeting
        );

        var editable =
            currentUserCanManageMeetings &&
            (
                currentMeetingIsNew ||
                currentMeetingIsEditing
            );

        meetingMeta.style.display =
            currentMeetingIsNew
                ? "grid"
                : "none";

        meetingDateTime.disabled =
            !currentMeetingIsNew;

        if (meetingScopeField) {
            meetingScopeField.style.display =
                currentMeetingIsNew
                    ? "block"
                    : "none";
        }

        if (currentMeetingIsNew) {
            renderMeetingScopeTags();
            renderMeetingScopeOptions(
                meetingScopeSearch
                    ? meetingScopeSearch.value
                    : ""
            );
        } else {
            closeMeetingScopeDropdown();
        }

        var isClosed =
            !!currentMeeting.closed;

        if (isClosed) {
            editable = false;
        }

        meetingSummary.value =
            currentMeeting.summary ||
            "";

        meetingSummary.readOnly =
            !editable;

        meetingEdit.style.display =
            currentUserCanManageMeetings &&
            !currentMeetingIsNew &&
            !currentMeetingIsEditing &&
            !isClosed
                ? "inline-flex"
                : "none";

        meetingSave.style.display =
            editable
                ? "inline-flex"
                : "none";

        meetingDelete.style.display =
            !currentUserCanManageMeetings ||
            currentMeetingIsNew ||
            currentMeetingIsEditing
                ? "none"
                : "inline-flex";

        meetingCloseMeeting.style.display =
            editable &&
            !isClosed
                ? "inline-flex"
                : "none";

        renderMeetingParticipants();
    }

    function openMeetingDetail(
        meeting,
        isNew
    ) {

        currentMeeting =
            cloneLocalJson(
                meeting
            );

        normalizeMeetingSectionsLocal(
            currentMeeting
        );

        currentMeetingIsNew =
            !!isNew;

        currentMeetingIsEditing =
            !!isNew;

        currentMeetingEditParticipantSnapshot =
            currentMeetingIsNew
                ? createMeetingParticipantEditSnapshot(
                    currentMeeting
                )
                : null;

        meetingsListView.classList.remove(
            "visible"
        );

        meetingDetailView.classList.add(
            "visible"
        );

        meetingListFooter.classList.remove(
            "visible"
        );

        meetingDetailFooter.classList.add(
            "visible"
        );

        meetingCreate.style.display =
            "none";

        meetingTitle.textContent =
            currentMeetingIsNew
                ? "Новое собрание"
                : formatMeetingDateTime(
                    currentMeeting.dateTime
                );

        meetingSubtitle.textContent =
            getMeetingNodeName(
                currentMeetingsNode
            );

        meetingDateTime.value =
            normalizeMeetingDateTime(
                currentMeeting.dateTime
            );

        if (meetingScopeSearch) {
            meetingScopeSearch.value = "";
        }

        updateMeetingDetailMode();
    }

    function getMeetingParticipantSnapshotKey(
        participant,
        meeting
    ) {

        if (!participant) {
            return "";
        }

        var sectionNodeId =
            getParticipantMeetingSectionIdLocal(
                participant,
                meeting || currentMeeting
            );

        var userKey = "";

        if (
            participant.userId !== null &&
            participant.userId !== undefined &&
            String(participant.userId) !== ""
        ) {
            userKey =
                "id:" +
                String(participant.userId);
        } else {
            userKey =
                "name:" +
                String(
                    participant.userName ||
                    ""
                );
        }

        return (
            "section:" +
            sectionNodeId +
            "|" +
            userKey
        );
    }

    function createMeetingParticipantEditSnapshot(
        meeting
    ) {

        var result = {};

        normalizeMeetingSectionsLocal(
            meeting
        );

        var participants =
            meeting &&
            Array.isArray(meeting.participants)
                ? meeting.participants
                : [];

        for (
            var i = 0;
            i < participants.length;
            i++
        ) {

            var participant =
                participants[i];

            var key =
                getMeetingParticipantSnapshotKey(
                    participant,
                    meeting
                );

            if (!key) {
                continue;
            }

            result[key] = {
                previousResult:
                    participant.previousResult || "",
                previousComment:
                    participant.previousComment || "",
                currentObligation:
                    participant.currentObligation || "",
                currentResult:
                    participant.currentResult || "",
                currentComment:
                    participant.currentComment || ""
            };
        }

        return result;
    }

    function buildMeetingParticipantFieldChanges() {

        if (
            !currentMeeting ||
            !currentMeetingIsEditing ||
            !currentMeetingEditParticipantSnapshot
        ) {
            return [];
        }

        normalizeMeetingSectionsLocal(
            currentMeeting
        );

        var result = [];

        var participants =
            Array.isArray(
                currentMeeting.participants
            )
                ? currentMeeting.participants
                : [];

        for (
            var i = 0;
            i < participants.length;
            i++
        ) {

            var participant =
                participants[i];

            var key =
                getMeetingParticipantSnapshotKey(
                    participant,
                    currentMeeting
                );

            var before =
                currentMeetingEditParticipantSnapshot[
                    key
                ] || {
                    previousResult: "",
                    previousComment: "",
                    currentObligation: "",
                    currentResult: "",
                    currentComment: ""
                };

            var data = {};

            var previousResult =
                participant.previousResult || "";
            var previousComment =
                participant.previousComment || "";
            var currentObligation =
                participant.currentObligation || "";
            var currentResult =
                participant.currentResult || "";
            var currentComment =
                participant.currentComment || "";

            if (
                String(previousResult) !==
                String(before.previousResult || "")
            ) {
                data.previousResult =
                    previousResult;
            }

            if (
                String(previousComment) !==
                String(before.previousComment || "")
            ) {
                data.previousComment =
                    previousComment;
            }

            if (!currentMeetingIsNew) {

                if (
                    String(currentObligation) !==
                    String(before.currentObligation || "")
                ) {
                    data.currentObligation =
                        currentObligation;
                }

                if (
                    String(currentResult) !==
                    String(before.currentResult || "")
                ) {
                    data.currentResult =
                        currentResult;
                }

                if (
                    String(currentComment) !==
                    String(before.currentComment || "")
                ) {
                    data.currentComment =
                        currentComment;
                }
            }

            if (
                Object.keys(data).length
            ) {
                result.push({
                    sectionNodeId:
                        getParticipantMeetingSectionIdLocal(
                            participant,
                            currentMeeting
                        ),
                    userId:
                        participant.userId || "",
                    userName:
                        participant.userName || "",
                    data:
                        data
                });
            }
        }

        return result;
    }



    async function saveMeetingParticipantFieldChanges(
        changes
    ) {

        if (
            !currentMeeting
        ) {
            return;
        }

        /*
         * Не вызываем отдельную функцию Scripts для каждого участника.
         * Изменённые поля передаём вместе с объектом собрания во
         * временном служебном поле. saveKVCMeeting() на стороне TS
         * сам сохранит эти изменения в отдельные ключи участников и
         * удалит служебное поле перед записью самого собрания.
         *
         * Это устраняет зависимость от дополнительного публичного
         * метода updateKVCMeetingParticipantFields в Laravel.
         */

        currentMeeting.__participantFieldChanges =
            Array.isArray(
                changes
            )
                ? changes
                : [];
    }


    function editCurrentMeeting() {

        if (
            !currentUserCanManageMeetings ||
            !currentMeeting ||
            currentMeetingIsNew
        ) {
            return;
        }

        currentMeetingEditParticipantSnapshot =
            createMeetingParticipantEditSnapshot(
                currentMeeting
            );

        currentMeetingIsEditing =
            true;

        updateMeetingDetailMode();
    }


    meetingSummary.oninput =
        function () {

            if (
                currentMeeting &&
                (
                    currentMeetingIsNew ||
                    currentMeetingIsEditing
                ) &&
                !currentMeeting.closed
            ) {

                currentMeeting.summary =
                    meetingSummary.value;
            }
        };


    function setMeetingFullscreen(enabled) {

        meetingModal.classList.toggle(
            "fullscreen",
            !!enabled
        );

        meetingFullscreen.title =
            enabled
                ? "Свернуть окно"
                : "На весь экран";

        meetingFullscreen.textContent =
            enabled
                ? "↙"
                : "⛶";
    }


    async function openMeetings(node) {

        closeAllPopups();

        setMeetingFullscreen(
            false
        );

        currentMeetingsNode =
            node;

        currentMeetings =
            [];

        currentMeeting =
            null;

        currentMeetingIsNew =
            false;

        currentMeetingIsEditing =
            false;

        currentMeetingEditParticipantSnapshot =
            null;

        currentUserCanManageMeetings =
            canCurrentUserManageMeetingsLocal(
                node
            );

        meetingOverlay.classList.add(
            "open"
        );

        showMeetingListView();

        meetingsTableBody.innerHTML =
            '<tr><td colspan="6" class="kvc-meeting-empty">Загрузка собраний...</td></tr>';

        try {

            var loadedMeetings =
                await window.KvcApi.getKVCMeetings(
                    String(node.id)
                );

            currentMeetings =
                Array.isArray(
                    loadedMeetings
                )
                    ? loadedMeetings
                    : [];

            renderMeetingsList();

        } catch (error) {

            console.error(
                "Ошибка загрузки собраний",
                error
            );

            meetingsTableBody.innerHTML =
                '<tr><td colspan="6" class="kvc-meeting-empty">Не удалось загрузить собрания</td></tr>';
        }
    }


    function closeMeetings() {

        closePersonalMeetingEdit();

        closeMeetingScopeDropdown();

        setMeetingFullscreen(
            false
        );

        meetingOverlay.classList.remove(
            "open"
        );

        currentMeetingsNode =
            null;

        currentMeetings =
            [];

        currentMeeting =
            null;

        currentMeetingIsNew =
            false;

        currentMeetingIsEditing =
            false;

        currentMeetingEditParticipantSnapshot =
            null;

        currentUserCanManageMeetings =
            false;
    }


    function createMeetingFromList() {

        if (
            !currentMeetingsNode ||
            !currentUserCanManageMeetings
        ) {
            return;
        }

        var newMeeting =
            buildNewMeeting(
                currentMeetingsNode
            );

        openMeetingDetail(
            newMeeting,
            true
        );
    }


    async function saveCurrentMeeting() {

        if (
            !currentUserCanManageMeetings ||
            !currentMeetingsNode ||
            !currentMeeting ||
            (
                !currentMeetingIsNew &&
                !currentMeetingIsEditing
            )
        ) {
            return;
        }

        /*
         * Дата читается из поля только для нового собрания.
         * У существующего собрания dateTime никогда не меняется.
         */
        if (
            currentMeetingIsNew
        ) {

            var dateTimeValue =
                meetingDateTime.value;

            if (!dateTimeValue) {

                alert(
                    "Укажите дату собрания"
                );

                return;
            }

            currentMeeting.dateTime =
                dateTimeValue;

            applyPreviousMeetingData(
                currentMeeting,
                currentMeeting.dateTime,
                false
            );
        }

        normalizeMeetingSectionsLocal(
            currentMeeting
        );

        currentMeeting.summary =
            meetingSummary.value ||
            "";

        var participantFieldChanges =
            buildMeetingParticipantFieldChanges();

        meetingSave.disabled =
            true;

        meetingSave.textContent =
            "Сохранение...";

        try {

            /*
             * Сначала сохраняем только изменённые личные поля
             * участников в их отдельные записи участника.
             * Так полное редактирование собрания не конфликтует
             * с параллельным редактированием других участников.
             */
            await saveMeetingParticipantFieldChanges(
                participantFieldChanges
            );

            await window.KvcApi.saveKVCMeeting(
                String(
                    currentMeetingsNode.id
                ),
                currentMeeting
            );

            var loadedMeetings =
                await window.KvcApi.getKVCMeetings(
                    String(
                        currentMeetingsNode.id
                    )
                );

            currentMeetings =
                Array.isArray(
                    loadedMeetings
                )
                    ? loadedMeetings
                    : [];

            showMeetingListView();

            renderMeetingsList();

        } catch (error) {

            console.error(
                "Ошибка сохранения собрания",
                error
            );

            alert(
                error && error.message
                    ? error.message
                    : "Не удалось сохранить собрание"
            );

        } finally {

            meetingSave.disabled =
                false;

            meetingSave.textContent =
                "Сохранить";
        }
    }

    function validateMeetingStatusesForClose() {

        if (
            !currentMeeting ||
            !Array.isArray(
                currentMeeting.participants
            )
        ) {
            return false;
        }

        normalizeMeetingSectionsLocal(
            currentMeeting
        );

        for (
            var i = 0;
            i < currentMeeting.participants.length;
            i++
        ) {

            var participant =
                currentMeeting.participants[i];

            if (
                !participant.previousStatus ||
                !participant.attendance
            ) {
                return false;
            }
        }

        return true;
    }



    async function closeCurrentMeeting() {

        if (
            !currentUserCanManageMeetings ||
            !currentMeetingsNode ||
            !currentMeeting ||
            currentMeeting.closed ||
            (
                !currentMeetingIsNew &&
                !currentMeetingIsEditing
            )
        ) {
            return;
        }

        /*
         * У нового собрания дата фиксируется при первом
         * сохранении/закрытии. Для существующего она не меняется.
         */
        if (currentMeetingIsNew) {

            var dateTimeValue =
                meetingDateTime.value;

            if (!dateTimeValue) {

                alert(
                    "Укажите дату собрания"
                );

                return;
            }

            currentMeeting.dateTime =
                dateTimeValue;

            applyPreviousMeetingData(
                currentMeeting,
                currentMeeting.dateTime,
                false
            );
        }

        if (
            !validateMeetingStatusesForClose()
        ) {

            alert(
                "Не все статусы установлены"
            );

            return;
        }

        normalizeMeetingSectionsLocal(
            currentMeeting
        );

        currentMeeting.summary =
            meetingSummary.value ||
            "";

        var participantFieldChanges =
            buildMeetingParticipantFieldChanges();

        currentMeeting.closed =
            true;

        currentMeeting.closedAt =
            new Date().toISOString();

        currentMeeting.closedBy =
            currentUserData
                ? currentUserData.id
                : null;

        currentMeeting.closedByName =
            currentUserData
                ? (
                    currentUserData.fio ||
                    currentUserData.name ||
                    ""
                )
                : "";

        meetingCloseMeeting.disabled =
            true;

        meetingCloseMeeting.textContent =
            "Закрытие...";

        try {

            await saveMeetingParticipantFieldChanges(
                participantFieldChanges
            );

            await window.KvcApi.saveKVCMeeting(
                String(
                    currentMeetingsNode.id
                ),
                currentMeeting
            );

            var loadedMeetings =
                await window.KvcApi.getKVCMeetings(
                    String(
                        currentMeetingsNode.id
                    )
                );

            currentMeetings =
                Array.isArray(
                    loadedMeetings
                )
                    ? loadedMeetings
                    : [];

            var closedMeeting =
                null;

            for (
                var i = 0;
                i < currentMeetings.length;
                i++
            ) {

                if (
                    String(currentMeetings[i].id) ===
                    String(currentMeeting.id)
                ) {
                    closedMeeting =
                        currentMeetings[i];
                    break;
                }
            }

            if (closedMeeting) {

                openMeetingDetail(
                    closedMeeting,
                    false
                );

            } else {

                showMeetingListView();
                renderMeetingsList();
            }

        } catch (error) {

            currentMeeting.closed =
                false;

            console.error(
                "Ошибка закрытия собрания",
                error
            );

            alert(
                error && error.message
                    ? error.message
                    : "Не удалось закрыть собрание"
            );

        } finally {

            meetingCloseMeeting.disabled =
                false;

            meetingCloseMeeting.textContent =
                "Закрыть собрание";
        }
    }


    async function deleteCurrentMeeting() {

        if (
            !currentUserCanManageMeetings ||
            !currentMeetingsNode ||
            !currentMeeting ||
            currentMeetingIsNew
        ) {
            return;
        }

        if (
            !window.confirm(
                "Удалить собрание от " +
                formatMeetingDateTime(
                    currentMeeting.dateTime
                ) +
                "?"
            )
        ) {
            return;
        }

        meetingDelete.disabled =
            true;

        meetingDelete.textContent =
            "Удаление...";

        try {

            await window.KvcApi.deleteKVCMeeting(
                String(
                    currentMeetingsNode.id
                ),
                String(
                    currentMeeting.id
                )
            );

            var loadedMeetings =
                await window.KvcApi.getKVCMeetings(
                    String(
                        currentMeetingsNode.id
                    )
                );

            currentMeetings =
                Array.isArray(
                    loadedMeetings
                )
                    ? loadedMeetings
                    : [];

            showMeetingListView();

            renderMeetingsList();

        } catch (error) {

            console.error(
                "Ошибка удаления собрания",
                error
            );

            alert(
                error && error.message
                    ? error.message
                    : "Не удалось удалить собрание"
            );

        } finally {

            meetingDelete.disabled =
                false;

            meetingDelete.textContent =
                "Удалить";
        }
    }


    /* =========================================================
       ACTION ICONS
       ========================================================= */

    function createActionIcons(node, options) {

        options = options || {};

        var icons =
            document.createElement(
                "div"
            );


        icons.className =
            "kvc-icons";


        /* =====================================================
           INFO
           ЛЕВЕЕ КАЛЕНДАРЯ
           ===================================================== */

        var infoWrap =
            document.createElement(
                "div"
            );


        infoWrap.className =
            "kvc-icon-wrap";


        var infoButton =
            createKvcIconButton(
                "info",
                "Дополнительно"
            );


        infoButton.onclick =
            function (event) {

                event.stopPropagation();


                openInfoPopup(
                    infoWrap,
                    infoButton,
                    node
                );

            };


        infoWrap.appendChild(
            infoButton
        );


        icons.appendChild(
            infoWrap
        );


        /* =====================================================
           CALENDAR
           В таблице скрывается: период уже есть отдельной колонкой.
           ===================================================== */

        if (
            !options.hideCalendar
        ) {

            var calendarWrap =
                document.createElement(
                    "div"
                );


            calendarWrap.className =
                "kvc-icon-wrap";


            var calendarButton =
                createKvcIconButton(
                    "calendar",
                    "Период"
                );


            calendarButton.onclick =
                function (event) {

                    event.stopPropagation();


                    openCalendarPopup(
                        calendarWrap,
                        calendarButton,
                        node
                    );

                };


            calendarWrap.appendChild(
                calendarButton
            );


            icons.appendChild(
                calendarWrap
            );
        }


        /* =====================================================
           USERS
           ===================================================== */

        var usersWrap =
            document.createElement(
                "div"
            );


        usersWrap.className =
            "kvc-icon-wrap";


        var usersButton =
            createKvcIconButton(
                "users",
                "Участники"
            );


        usersButton.onclick =
            function (event) {

                event.stopPropagation();


                openUsersPopup(
                    usersWrap,
                    usersButton,
                    node
                );

            };


        usersWrap.appendChild(
            usersButton
        );


        icons.appendChild(
            usersWrap
        );


        /* =====================================================
           SETTINGS
           ===================================================== */

        var settingsWrap =
            document.createElement(
                "div"
            );


        settingsWrap.className =
            "kvc-icon-wrap";


        var settingsButton =
            createKvcIconButton(
                "settings",
                isAdmin
                    ? "Настройки"
                    : "Внести текущее значение"
            );


        settingsButton.onclick =
            function (event) {

                event.stopPropagation();


                openSettings(
                    node
                );

            };


        settingsWrap.appendChild(
            settingsButton
        );


        icons.appendChild(
            settingsWrap
        );


        return icons;
    }


    /* =========================================================
       СОРТИРОВКА ДОЧЕРНИХ ЭЛЕМЕНТОВ
       ========================================================= */

    function clearDragMarkers() {

        var marked =
            widget.querySelectorAll(
                ".kvc-drag-before, .kvc-drag-after, .kvc-dragging"
            );

        for (
            var i = 0;
            i < marked.length;
            i++
        ) {
            marked[i].classList.remove(
                "kvc-drag-before",
                "kvc-drag-after",
                "kvc-dragging"
            );
        }
    }


    function findNodeByIdLocal(
        node,
        id
    ) {

        if (!node) {
            return null;
        }

        if (
            String(node.id) ===
            String(id)
        ) {
            return node;
        }

        var children =
            node.children || [];

        for (
            var i = 0;
            i < children.length;
            i++
        ) {

            var found =
                findNodeByIdLocal(
                    children[i],
                    id
                );

            if (found) {
                return found;
            }
        }

        return null;
    }


    async function moveSiblingNode(
        parentNode,
        draggedId,
        targetId,
        insertAfter
    ) {

        if (
            !isAdmin ||
            !parentNode ||
            !Array.isArray(parentNode.children) ||
            String(draggedId) === String(targetId)
        ) {
            return;
        }

        var children =
            parentNode.children;

        var fromIndex = -1;
        var targetIndex = -1;

        for (
            var i = 0;
            i < children.length;
            i++
        ) {

            if (
                String(children[i].id) ===
                String(draggedId)
            ) {
                fromIndex = i;
            }

            if (
                String(children[i].id) ===
                String(targetId)
            ) {
                targetIndex = i;
            }
        }

        if (
            fromIndex < 0 ||
            targetIndex < 0
        ) {
            return;
        }

        var moved =
            children.splice(
                fromIndex,
                1
            )[0];

        targetIndex = -1;

        for (
            var j = 0;
            j < children.length;
            j++
        ) {
            if (
                String(children[j].id) ===
                String(targetId)
            ) {
                targetIndex = j;
                break;
            }
        }

        if (targetIndex < 0) {
            children.push(moved);
        } else {
            children.splice(
                targetIndex +
                    (insertAfter ? 1 : 0),
                0,
                moved
            );
        }

        renderTree();

        try {

            await window.KvcApi.updateKVC(
                treeData
            );

        } catch (error) {

            console.error(
                "Ошибка сохранения порядка КВЦ / ОП",
                error
            );

            var freshTree =
                await window.KvcApi.getKVC();

            if (freshTree) {
                treeData = freshTree;
                renderTree();
            }
        }
    }


    function isDragInsertAfter(
        card,
        event
    ) {

        var rect =
            card.getBoundingClientRect();


        if (
            currentView ===
            "tree-vertical"
        ) {

            return (
                event.clientX >
                rect.left +
                rect.width / 2
            );
        }


        return (
            event.clientY >
            rect.top +
            rect.height / 2
        );
    }


    /* =========================================================
       НАДЁЖНОЕ ПЕРЕТАСКИВАНИЕ ЧЕРЕЗ POINTER EVENTS

       В Laravel нативный HTML5 drag/drop может перехватываться
       оболочкой страницы. Поэтому сортировка выполняется по
       pointerdown / pointermove / pointerup.
       ========================================================= */

    function clearDragPositionMarkers() {

        var marked =
            widget.querySelectorAll(
                ".kvc-drag-before, .kvc-drag-after"
            );


        for (
            var i = 0;
            i < marked.length;
            i++
        ) {

            marked[i].classList.remove(
                "kvc-drag-before",
                "kvc-drag-after"
            );
        }
    }


    function resetPointerDrag() {

        clearDragMarkers();


        draggedNodeId = null;

        draggedParentId = null;

        draggedSourceCard = null;

        draggedTargetNodeId = null;

        draggedTargetAfter = false;

        draggedPointerId = null;


        if (document.body) {
            document.body.style.userSelect = "";
        }
    }


    function startPointerDrag(
        event,
        node,
        parentNode,
        card
    ) {

        if (
            !isAdmin ||
            !parentNode
        ) {
            return;
        }


        if (
            event.button !== undefined &&
            event.button !== 0
        ) {
            return;
        }


        event.preventDefault();
        event.stopPropagation();


        draggedNodeId =
            String(node.id);


        draggedParentId =
            String(parentNode.id);


        draggedSourceCard =
            card;


        draggedTargetNodeId =
            null;


        draggedTargetAfter =
            false;


        draggedPointerId =
            event.pointerId !== undefined
                ? event.pointerId
                : null;


        clearDragPositionMarkers();


        card.classList.add(
            "kvc-dragging"
        );


        if (document.body) {
            document.body.style.userSelect = "none";
        }


        if (
            event.currentTarget &&
            event.currentTarget.setPointerCapture &&
            draggedPointerId !== null
        ) {

            try {
                event.currentTarget.setPointerCapture(
                    draggedPointerId
                );
            } catch (captureError) {
                /* Для сортировки захват не обязателен. */
            }
        }
    }


    function updatePointerDrag(event) {

        if (
            !draggedNodeId ||
            !draggedParentId
        ) {
            return;
        }


        if (
            draggedPointerId !== null &&
            event.pointerId !== undefined &&
            event.pointerId !== draggedPointerId
        ) {
            return;
        }


        event.preventDefault();


        clearDragPositionMarkers();


        draggedTargetNodeId =
            null;


        var element =
            document.elementFromPoint(
                event.clientX,
                event.clientY
            );


        var targetCard =
            element && element.closest
                ? element.closest(
                    ".kvc-card"
                )
                : null;


        if (
            !targetCard ||
            !widget.contains(targetCard)
        ) {
            return;
        }


        var targetNodeId =
            targetCard.getAttribute(
                "data-node-id"
            );


        var targetParentId =
            targetCard.getAttribute(
                "data-parent-id"
            );


        if (
            !targetNodeId ||
            !targetParentId ||
            String(targetParentId) !==
                String(draggedParentId) ||
            String(targetNodeId) ===
                String(draggedNodeId)
        ) {
            return;
        }


        draggedTargetAfter =
            isDragInsertAfter(
                targetCard,
                event
            );


        draggedTargetNodeId =
            String(targetNodeId);


        targetCard.classList.add(
            draggedTargetAfter
                ? "kvc-drag-after"
                : "kvc-drag-before"
        );
    }


    function finishPointerDrag(event) {

        if (
            !draggedNodeId ||
            !draggedParentId
        ) {
            return;
        }


        if (
            draggedPointerId !== null &&
            event.pointerId !== undefined &&
            event.pointerId !== draggedPointerId
        ) {
            return;
        }


        event.preventDefault();


        var sourceId =
            draggedNodeId;


        var parentId =
            draggedParentId;


        var targetId =
            draggedTargetNodeId;


        var insertAfter =
            draggedTargetAfter;


        resetPointerDrag();


        if (!targetId) {
            return;
        }


        var parentNode =
            findNodeByIdLocal(
                treeData,
                parentId
            );


        if (!parentNode) {
            return;
        }


        moveSiblingNode(
            parentNode,
            sourceId,
            targetId,
            insertAfter
        );
    }


    function cancelPointerDrag() {

        if (!draggedNodeId) {
            return;
        }


        resetPointerDrag();
    }


    /* =========================================================
       CARD
       ========================================================= */

    function createCard(node, parentNode) {

        var hasChildren =
            node.children &&
            node.children.length > 0;


        var card =
            document.createElement(
                "div"
            );


        card.className =
            "kvc-card " +
            node.type;


        card.setAttribute(
            "data-node-id",
            node.id
        );


        if (parentNode) {

            card.setAttribute(
                "data-parent-id",
                parentNode.id
            );
        }


        if (
            isAdmin &&
            parentNode
        ) {

            card.ondragover =
                function (event) {

                    if (
                        !draggedNodeId ||
                        draggedParentId !==
                            String(parentNode.id) ||
                        draggedNodeId ===
                            String(node.id)
                    ) {
                        return;
                    }

                    event.preventDefault();

                    clearDragMarkers();

                    var after =
                        isDragInsertAfter(
                            card,
                            event
                        );

                    card.classList.add(
                        after
                            ? "kvc-drag-after"
                            : "kvc-drag-before"
                    );

                    if (event.dataTransfer) {
                        event.dataTransfer.dropEffect =
                            "move";
                    }
                };


            card.ondrop =
                function (event) {

                    if (
                        !draggedNodeId ||
                        draggedParentId !==
                            String(parentNode.id) ||
                        draggedNodeId ===
                            String(node.id)
                    ) {
                        return;
                    }

                    event.preventDefault();
                    event.stopPropagation();

                    var after =
                        isDragInsertAfter(
                            card,
                            event
                        );

                    var sourceId =
                        draggedNodeId;

                    draggedNodeId = null;
                    draggedParentId = null;

                    clearDragMarkers();

                    moveSiblingNode(
                        parentNode,
                        sourceId,
                        node.id,
                        after
                    );
                };
        }


        var top =
            document.createElement(
                "div"
            );


        top.className =
            "kvc-card-top";


        var topLine =
            document.createElement(
                "div"
            );


        topLine.className =
            "kvc-top-line";


        var topLeft =
            document.createElement(
                "div"
            );


        topLeft.className =
            "kvc-top-left";


        if (
            isAdmin &&
            parentNode
        ) {

            var dragHandle =
                document.createElement(
                    "span"
                );


            dragHandle.className =
    "kvc-drag-handle";


dragHandle.textContent =
    "";


            dragHandle.title =
                "Перетащить";


            /*
             * Нативный draggable намеренно выключен.
             * Внутри Laravel надёжнее работает pointer-based
             * сортировка, которая не зависит от HTML5 drag/drop.
             */

            dragHandle.draggable =
                false;


            dragHandle.setAttribute(
                "draggable",
                "false"
            );


            dragHandle.onclick =
                function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                };


            dragHandle.onpointerdown =
                function (event) {

                    startPointerDrag(
                        event,
                        node,
                        parentNode,
                        card
                    );
                };


            topLeft.appendChild(
                dragHandle
            );
        }


        var badge =
            document.createElement(
                "div"
            );


        badge.className =
            "kvc-type-badge";


        badge.textContent =
    node.typeName === "↑ОП / КВЦ↓"
        ? "ОП / КВЦ"
        : node.typeName;


        topLeft.appendChild(
            badge
        );


        topLine.appendChild(
            topLeft
        );


        topLine.appendChild(
            createActionIcons(
                node
            )
        );


        top.appendChild(
            topLine
        );


        var title =
            document.createElement(
                "div"
            );


        title.className =
            "kvc-card-title";


        title.textContent =
            node.title;


        top.appendChild(
            title
        );


        card.appendChild(
            top
        );


        if (
            node.description
        ) {

            var description =
                document.createElement(
                    "div"
                );


            description.className =
                "kvc-goal";


            description.textContent =
                node.description;


            card.appendChild(
                description
            );
        }


        var calculated =
            getCalculatedMetrics(
                node
            );


        var planValue =
            calculated.plan;


        var factValue =
            calculated.fact;


        var factStatus =
            getFactStatus(
                node,
                planValue,
                factValue
            );


        var planText =
            formatIndicatorValue(
                node,
                planValue
            );


        var factText =
            formatIndicatorValue(
                node,
                factValue
            );


        var metrics =
            document.createElement(
                "div"
            );


        metrics.className =
            "kvc-metrics";


        metrics.innerHTML =

            '<div class="kvc-metric plan">' +

                '<div class="kvc-metric-label">' +
                    'План' +
                '</div>' +

                '<div class="kvc-metric-value' +
                    (
                        isLongIndicatorValue(
                            node,
                            planValue
                        )
                            ? ' long'
                            : ''
                    ) +
                '">' +

                    formatIndicatorValueHtml(
                        node,
                        planValue
                    ) +

                '</div>' +

            '</div>' +


            '<div class="kvc-metric fact' +
                (
                    factStatus
                        ? ' ' + factStatus
                        : ''
                ) +
            '">' +

                '<div class="kvc-metric-label">' +
                    'Факт' +
                '</div>' +

                '<div class="kvc-metric-value' +
                    (
                        isLongIndicatorValue(
                            node,
                            factValue
                        )
                            ? ' long'
                            : ''
                    ) +
                '">' +

                    formatIndicatorValueHtml(
                        node,
                        factValue
                    ) +

                '</div>' +

            '</div>' +


            '<div class="kvc-metric audit">' +

                '<div class="kvc-metric-label">' +
                    'Автоаудит' +
                '</div>' +

                '<div class="kvc-metric-value">' +

                    escapeHtml(
                        node.autoaudit ||
                        "—"
                    ) +

                '</div>' +

            '</div>';


        card.appendChild(
            metrics
        );


        var bottom =
            document.createElement(
                "div"
            );


        bottom.className =
            "kvc-card-bottom";


        if (
            node.type !== "op"
        ) {

            var meetings =
                document.createElement(
                    "button"
                );


            meetings.type =
                "button";


            meetings.className =
                "kvc-meetings-btn";


            meetings.textContent =
                "Собрания";


            meetings.onclick =
                function (event) {

                    event.stopPropagation();

                    openMeetings(
                        node
                    );
                };


            bottom.appendChild(
                meetings
            );
        }


        var owner =
            document.createElement(
                "div"
            );


        owner.className =
            "kvc-owner";


        if (
            node.type === "op"
        ) {
            owner.style.marginLeft =
                "auto";
        }


        owner.innerHTML =

            '<div class="kvc-owner-label">' +
                'Владелец' +
            '</div>' +

            '<div class="kvc-owner-value">' +

                escapeHtml(
                    node.owner ||
                    "—"
                ) +

            '</div>';


        bottom.appendChild(
            owner
        );


        card.appendChild(
            bottom
        );


        if (
            hasChildren
        ) {

            var toggle =
                document.createElement(
                    "button"
                );


            toggle.type =
                "button";


            toggle.className =
                "kvc-toggle";


            toggle.textContent =
                collapsed[node.id]
                    ? "+"
                    : "−";


            toggle.onclick =
                function (event) {

                    event.stopPropagation();


                    closeAllPopups();


                    if (
                        collapsed[node.id]
                    ) {

                        delete collapsed[
                            node.id
                        ];

                    } else {

                        collapsed[
                            node.id
                        ] = true;
                    }


                    renderTree();

                    scheduleKvcUiStateSave(20);

                };


            card.appendChild(
                toggle
            );
        }


        return card;
    }


    /* =========================================================
       TREE
       ========================================================= */

    function createBranch(node, parentNode) {

        var branch =
            document.createElement(
                "div"
            );


        branch.className =
            "kvc-branch";


        branch.appendChild(
            createCard(
                node,
                parentNode
            )
        );


        if (
            node.children &&
            node.children.length &&
            !collapsed[node.id]
        ) {

            var children =
                document.createElement(
                    "div"
                );


            children.className =
                "kvc-children";


            for (
                var i = 0;
                i < node.children.length;
                i++
            ) {

                children.appendChild(
                    createBranch(
                        node.children[i],
                        node
                    )
                );
            }


            branch.appendChild(
                children
            );
        }


        return branch;
    }


    function getConnections(
        node,
        result
    ) {

        result =
            result || [];


        if (
            !node.children ||
            !node.children.length ||
            collapsed[node.id]
        ) {

            return result;
        }


        for (
            var i = 0;
            i < node.children.length;
            i++
        ) {

            result.push({

                parent:
                    node.id,

                child:
                    node.children[i].id

            });


            getConnections(
                node.children[i],
                result
            );
        }


        return result;
    }


    function drawConnections() {

        while (
            svg.firstChild
        ) {

            svg.removeChild(
                svg.firstChild
            );
        }


        if (
            currentView ===
            "table"
        ) {
            return;
        }


        var canvasRect =
            canvas
                .getBoundingClientRect();


        var width =
            Math.max(
                canvas.scrollWidth,
                canvas.clientWidth
            );


        var height =
            Math.max(
                canvas.scrollHeight,
                canvas.clientHeight
            );


        svg.setAttribute(
            "width",
            width
        );


        svg.setAttribute(
            "height",
            height
        );


        var connections =
            getConnections(
                treeData,
                []
            );


        var isVertical =
            currentView ===
            "tree-vertical";


        for (
            var i = 0;
            i < connections.length;
            i++
        ) {

            var connection =
                connections[i];


            var parent =
                tree.querySelector(
                    '[data-node-id="' +
                    connection.parent +
                    '"]'
                );


            var child =
                tree.querySelector(
                    '[data-node-id="' +
                    connection.child +
                    '"]'
                );


            if (
                !parent ||
                !child
            ) {
                continue;
            }


            var parentRect =
                parent
                    .getBoundingClientRect();


            var childRect =
                child
                    .getBoundingClientRect();


            var x1;
            var y1;
            var x2;
            var y2;
            var pathData;


            if (
                isVertical
            ) {

                x1 =
                    parentRect.left -
                    canvasRect.left +
                    parentRect.width / 2;


                y1 =
                    parentRect.bottom -
                    canvasRect.top;


                x2 =
                    childRect.left -
                    canvasRect.left +
                    childRect.width / 2;


                y2 =
                    childRect.top -
                    canvasRect.top;


                var middleY =
                    y1 +
                    (
                        y2 -
                        y1
                    ) / 2;


                pathData =
                    "M " +
                    x1 +
                    " " +
                    y1 +

                    " V " +
                    middleY +

                    " H " +
                    x2 +

                    " V " +
                    y2;

            } else {

                x1 =
                    parentRect.right -
                    canvasRect.left;


                y1 =
                    parentRect.top -
                    canvasRect.top +
                    parentRect.height / 2;


                x2 =
                    childRect.left -
                    canvasRect.left;


                y2 =
                    childRect.top -
                    canvasRect.top +
                    childRect.height / 2;


                var middleX =
                    x1 +
                    (
                        x2 -
                        x1
                    ) / 2;


                pathData =
                    "M " +
                    x1 +
                    " " +
                    y1 +

                    " H " +
                    middleX +

                    " V " +
                    y2 +

                    " H " +
                    x2;
            }


            var path =
                createSvgElement(
                    "path"
                );


            path.setAttribute(
                "class",
                "kvc-line"
            );


            path.setAttribute(
                "d",
                pathData
            );


            svg.appendChild(
                path
            );


            var dot =
                createSvgElement(
                    "circle"
                );


            dot.setAttribute(
                "class",
                "kvc-line-dot"
            );


            dot.setAttribute(
                "cx",
                x2
            );


            dot.setAttribute(
                "cy",
                y2
            );


            dot.setAttribute(
                "r",
                3
            );


            svg.appendChild(
                dot
            );
        }
    }


    function renderTree() {

        closeAllPopups();


        tree.innerHTML =
            "";


        if (
            !treeData
        ) {

            tree.innerHTML =
                '<div style="padding:20px;color:#8a94a5;font-size:12px;">КВЦ пока не загружены</div>';

            return;
        }


        tree.appendChild(
            createBranch(
                treeData,
                null
            )
        );


        setTimeout(
            function () {

                drawConnections();

            },
            0
        );
    }


    /* =========================================================
       TABLE
       ========================================================= */

    function flattenTree(
        node,
        level,
        result
    ) {

        result =
            result || [];


        result.push({

            node:
                node,

            level:
                level

        });


        if (
            node.children &&
            node.children.length
        ) {

            for (
                var i = 0;
                i < node.children.length;
                i++
            ) {

                flattenTree(
                    node.children[i],
                    level + 1,
                    result
                );
            }
        }


        return result;
    }


    function renderTable() {

        closeAllPopups();


        tableBody.innerHTML =
            "";


        if (
            !treeData
        ) {

            return;
        }


        var rows =
            flattenTree(
                treeData,
                0,
                []
            );


        for (
            var i = 0;
            i < rows.length;
            i++
        ) {

            createTableRow(
                rows[i].node,
                rows[i].level
            );
        }
    }


    function createTableRow(
        node,
        level
    ) {

        var row =
            document.createElement(
                "tr"
            );


        /* TYPE */

        var typeCell =
            document.createElement(
                "td"
            );


        var badge =
            document.createElement(
                "span"
            );


        badge.className =
            "kvc-table-badge " +
            node.type;


        badge.textContent =
            node.typeName;


        typeCell.appendChild(
            badge
        );


        row.appendChild(
            typeCell
        );


        /* NAME */

        var nameCell =
            document.createElement(
                "td"
            );


        var nameWrap =
            document.createElement(
                "div"
            );


        nameWrap.className =
            "kvc-table-name-wrap " +
            "kvc-table-level-" +
            Math.min(
                level,
                4
            );


        if (
            level > 0
        ) {

            var treeLine =
                document.createElement(
                    "span"
                );


            treeLine.className =
                "kvc-table-tree-line";


            nameWrap.appendChild(
                treeLine
            );
        }


        var nameText =
            document.createElement(
                "span"
            );


        nameText.className =
            "kvc-table-name-text";


        nameText.textContent =
            node.description ||
            "—";


        nameText.title =
            node.description ||
            "";


        nameWrap.appendChild(
            nameText
        );


        nameCell.appendChild(
            nameWrap
        );


        row.appendChild(
            nameCell
        );


        var calculated =
            getCalculatedMetrics(
                node
            );


        var planValue =
            calculated.plan;


        var factValue =
            calculated.fact;


        var factStatus =
            getFactStatus(
                node,
                planValue,
                factValue
            );


        /* PLAN */

        var planCell =
            document.createElement(
                "td"
            );


        planCell.className =
            "kvc-table-plan";


        planCell.innerHTML =
            '<span class="kvc-table-value-html">' +
                formatIndicatorValueHtml(
                    node,
                    planValue
                ) +
            '</span>';


        row.appendChild(
            planCell
        );


        /* FACT */

        var factCell =
            document.createElement(
                "td"
            );


        factCell.className =
            "kvc-table-fact" +
            (
                factStatus
                    ? " " + factStatus
                    : ""
            );


        factCell.innerHTML =
            '<span class="kvc-table-value-html">' +
                formatIndicatorValueHtml(
                    node,
                    factValue
                ) +
            '</span>';


        row.appendChild(
            factCell
        );


        /* AUDIT */

        var auditCell =
            document.createElement(
                "td"
            );


        auditCell.className =
            "kvc-table-audit-value";


        auditCell.textContent =
            node.autoaudit ||
            "—";


        row.appendChild(
            auditCell
        );


        /* OWNER */

        var ownerCell =
            document.createElement(
                "td"
            );


        ownerCell.textContent =
            node.owner ||
            "—";


        row.appendChild(
            ownerCell
        );


        /* PERIOD */

        var periodCell =
            document.createElement(
                "td"
            );


        periodCell.textContent =
            formatDate(
                node.startDate
            ) +
            " — " +
            formatDate(
                node.endDate
            );


        row.appendChild(
            periodCell
        );


        /* ACTIONS */

        var actionsCell =
            document.createElement(
                "td"
            );


        var actions =
            createActionIcons(
                node,
                { hideCalendar: true }
            );


        actions.classList.add(
            "kvc-table-actions-wrap"
        );


        actionsCell.appendChild(
            actions
        );


        row.appendChild(
            actionsCell
        );


        tableBody.appendChild(
            row
        );
    }


    /* =========================================================
       EVENTS
       ========================================================= */

    document.addEventListener(
        "pointermove",
        updatePointerDrag,
        { passive: false }
    );


    document.addEventListener(
        "pointerup",
        finishPointerDrag,
        { passive: false }
    );


    document.addEventListener(
        "pointercancel",
        cancelPointerDrag
    );


    meetingPersonalClose.onclick =
        closePersonalMeetingEdit;


    meetingPersonalCancel.onclick =
        closePersonalMeetingEdit;


    meetingPersonalSave.onclick =
        savePersonalMeetingEdit;


    meetingPersonalModal.onclick =
        function (event) {
            event.stopPropagation();
        };


    meetingPersonalOverlay.onclick =
        function (event) {
            if (
                event.target ===
                meetingPersonalOverlay
            ) {
                event.stopPropagation();
            }
        };


    meetingFullscreen.onclick =
        function (event) {

            event.stopPropagation();

            setMeetingFullscreen(
                !meetingModal.classList.contains(
                    "fullscreen"
                )
            );
        };


    meetingClose.onclick =
        closeMeetings;


    meetingsCloseButton.onclick =
        closeMeetings;


    meetingCreate.onclick =
        createMeetingFromList;


    meetingBack.onclick =
        function () {

            /*
             * Если нажали «Отмена» во время редактирования
             * уже существующего собрания — не уходим к списку,
             * а возвращаемся в режим просмотра этого же собрания.
             * Несохранённые изменения отбрасываются: берём
             * исходную сохранённую версию из currentMeetings.
             */
            if (
                currentMeeting &&
                currentMeetingIsEditing &&
                !currentMeetingIsNew
            ) {

                var savedMeeting =
                    null;

                for (
                    var i = 0;
                    i < currentMeetings.length;
                    i++
                ) {

                    if (
                        String(currentMeetings[i].id) ===
                        String(currentMeeting.id)
                    ) {

                        savedMeeting =
                            currentMeetings[i];

                        break;
                    }
                }

                if (savedMeeting) {

                    openMeetingDetail(
                        savedMeeting,
                        false
                    );

                    return;
                }
            }

            /*
             * В обычном просмотре и при создании нового собрания
             * «Отмена» возвращает к списку собраний.
             */
            showMeetingListView();

            renderMeetingsList();
        };


    meetingDelete.onclick =
        deleteCurrentMeeting;


    meetingEdit.onclick =
        editCurrentMeeting;


    meetingCloseMeeting.onclick =
        closeCurrentMeeting;


    meetingSave.onclick =
        saveCurrentMeeting;


    meetingModal.onclick =
        function (event) {

            event.stopPropagation();
        };


    meetingOverlay.onclick =
        function (event) {

            if (
                event.target ===
                meetingOverlay
            ) {
                event.stopPropagation();
            }
        };



    meetingScopeSelection.onclick =
        function (event) {

            event.preventDefault();
            event.stopPropagation();

            if (
                !currentMeetingIsNew
            ) {
                return;
            }

            var isOpen =
                meetingScopeDropdown.classList.contains(
                    "open"
                );

            closeMeetingScopeDropdown();

            if (!isOpen) {
                meetingScopeDropdown.classList.add(
                    "open"
                );
                meetingScopeSelection.classList.add(
                    "open"
                );
                renderMeetingScopeOptions(
                    meetingScopeSearch.value || ""
                );
                setTimeout(
                    function () {
                        meetingScopeSearch.focus();
                    },
                    0
                );
            }
        };


    meetingScopeDropdown.onclick =
        function (event) {
            event.stopPropagation();
        };


    meetingScopeSearch.oninput =
        function () {
            renderMeetingScopeOptions(
                meetingScopeSearch.value
            );
        };


    meetingDateTime.onchange =
        function () {

            if (
                !currentMeeting ||
                !currentMeetingIsNew
            ) {
                return;
            }

            currentMeeting.dateTime =
                meetingDateTime.value;

            applyPreviousMeetingData(
                currentMeeting,
                currentMeeting.dateTime,
                true
            );

            renderMeetingParticipants();
        };


    editIndicatorType.onchange =
        function () {

            updateIndicatorFields();

            updateAdminCurrentValueField();

        };


    editTrueLabel.oninput =
        function () {

            if (
                editIndicatorType.value !==
                "boolean"
            ) {
                return;
            }


            var selected =
                editCurrentValueBoolean.value;


            buildBooleanSelect(
                editCurrentValueBoolean,
                getAdminTrueLabel(),
                getAdminFalseLabel(),

                selected === "false"
                    ? getAdminFalseLabel()
                    : getAdminTrueLabel()
            );

        };


    editFalseLabel.oninput =
        editTrueLabel.oninput;


    ownerControl.onclick =
        function (event) {

            event.stopPropagation();


            if (
                ownerDropdown
                    .classList
                    .contains("open")
            ) {

                closeOwnerDropdown();

            } else {

                openOwnerDropdown();

            }

        };


    ownerSearch.oninput =
        function () {

            renderOwnerOptions(
                ownerSearch.value
            );

        };


    meetingManagersControl.onclick =
        function (event) {

            event.stopPropagation();

        };


    meetingManagersSelection.onclick =
        function (event) {

            event.stopPropagation();


            if (
                meetingManagersDropdown
                    .classList
                    .contains(
                        "open"
                    )
            ) {

                closeMeetingManagersDropdown();

            } else {

                openMeetingManagersDropdown();
            }
        };


    meetingManagersSelection.onkeydown =
        function (event) {

            if (
                event.key !== "Enter" &&
                event.key !== " " &&
                event.key !== "ArrowDown"
            ) {
                return;
            }


            event.preventDefault();
            event.stopPropagation();


            if (
                !meetingManagersDropdown
                    .classList
                    .contains(
                        "open"
                    )
            ) {

                openMeetingManagersDropdown();
            }
        };


    meetingManagersDropdown.onclick =
        function (event) {

            event.stopPropagation();

        };


    meetingManagersSearch.oninput =
        function () {

            renderMeetingManagerOptions(
                meetingManagersSearch.value
            );

        };


    editNodeType.onchange =
        function () {

            updateMeetingManagersVisibility(
                currentSettingsNode,
                true
            );

        };


    ownerDropdown.onclick =
        function (event) {

            event.stopPropagation();

        };


    modalBody.addEventListener(
        "scroll",
        function () {

            positionOwnerDropdown();

            positionMeetingManagersDropdown();

        }
    );


    settingsAdd.onclick =
        addChildFromSettings;


    settingsDelete.onclick =
        deleteNodeFromSettings;


    settingsClose.onclick =
        closeSettings;


    settingsCancel.onclick =
        closeSettings;


    settingsSave.onclick =
        saveSettings;


    settingsModal.onclick =
        function (event) {

            event.stopPropagation();

        };


    settingsOverlay.onclick =
        function (event) {

            /*
             * Настройки не закрываются по клику вне окна.
             * Закрытие только по X / Отмена / Сохранить / Esc.
             */
            if (
                event.target ===
                settingsOverlay
            ) {
                event.stopPropagation();
            }

        };


    document.addEventListener(
        "click",
        function () {

            closeAllPopups();

            closeOwnerDropdown();

            closeMeetingManagersDropdown();

            closeMeetingScopeDropdown();

        }
    );


    document.addEventListener(
        "keydown",
        function (event) {

            if (
                event.key !== "Escape"
            ) {
                return;
            }


            if (
                meetingPersonalOverlay
                    .classList
                    .contains("open")
            ) {

                closePersonalMeetingEdit();

                return;
            }


            if (
                ownerDropdown
                    .classList
                    .contains("open")
            ) {

                closeOwnerDropdown();

                return;
            }


            if (
                meetingManagersDropdown
                    .classList
                    .contains("open")
            ) {

                closeMeetingManagersDropdown();

                return;
            }


            if (
                meetingScopeDropdown &&
                meetingScopeDropdown
                    .classList
                    .contains("open")
            ) {

                closeMeetingScopeDropdown();

                return;
            }


            closeAllPopups();


            if (
                meetingOverlay
                    .classList
                    .contains("open")
            ) {

                closeMeetings();

                return;
            }


            if (
                settingsOverlay
                    .classList
                    .contains("open")
            ) {

                closeSettings();

            }

        }
    );


    var resizeTimer =
        null;


    window.addEventListener(
        "resize",
        function () {

            clearTimeout(
                resizeTimer
            );


            resizeTimer =
                setTimeout(
                    function () {

                        drawConnections();

                        positionOwnerDropdown();

                        positionMeetingManagersDropdown();

                    },
                    100
                );

        }
    );


    /* =========================================================
       ЗАГРУЗКА РЕАЛЬНЫХ ДАННЫХ Laravel
       ========================================================= */

    async function loadRealData() {

        try {

            /*
             * Загружаем актуальное дерево из базы данных через API:
             * ваш TypeScript getKVC().
             */

            var loadedTree =
                await window.KvcApi.getKVC();


            if (
                loadedTree
            ) {

                treeData =
                    loadedTree;

            } else {

                treeData =
                    null;
            }


            /*
             * Реальные активные пользователи Laravel.
             * TypeScript возвращает { id, fio }, а интерфейс
             * внутри виджета использует { id, name }.
             */

            var loadedUsers =
                await window.KvcApi.getUsers();


            allUsers =
                Array.isArray(
                    loadedUsers
                )
                    ? loadedUsers.map(
                        function (user) {

                            return {

                                id:
                                    user.id,

                                name:
                                    user.fio ||
                                    user.name ||
                                    ""
                            };
                        }
                    )
                    : [];


            currentUserData =
                await window.KvcApi.getCurrentUserData();


            /*
             * Роль определяется самой Laravel.
             * Ручного переключателя «Админ / Не админ» больше нет.
             */
            isAdmin =
                !!(
                    currentUserData &&
                    currentUserData.isAdmin
                );


            /*
             * После получения реального дерева сворачиваем его
             * по умолчанию так же, как было раньше.
             */

            collapsed =
                {};


            var restoredCollapseState =
                applyPendingBaseUiState();


            if (
                treeData &&
                !restoredCollapseState
            ) {

                collapseAllByDefault(
                    treeData
                );
            }


            updateViewButtons();

            renderCurrentView();


            await restoreKvcUiStateAfterLoad();

        } catch (error) {

            console.error(
                "Ошибка загрузки данных КВЦ из Laravel",
                error
            );


            treeData =
                null;


            allUsers =
                [];


            currentUserData =
                null;


            isAdmin =
                false;


            applyPendingBaseUiState();

            updateViewButtons();

            renderCurrentView();

            await restoreKvcUiStateAfterLoad();
        }
    }


    /* =========================================================
       START
       ========================================================= */

    updateViewButtons();


    tree.innerHTML =
        '<div style="padding:20px;color:#8a94a5;font-size:12px;">Загрузка КВЦ...</div>';


    loadRealData();

})();
</script>
@endsection
