---
name: UPN Veteran Yogyakarta Enterprise Workspace
colors:
  surface: '#f8f9ff'
  surface-dim: '#cbdbf5'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eff4ff'
  surface-container: '#e5eeff'
  surface-container-high: '#dce9ff'
  surface-container-highest: '#d3e4fe'
  on-surface: '#0b1c30'
  on-surface-variant: '#43474e'
  inverse-surface: '#213145'
  inverse-on-surface: '#eaf1ff'
  outline: '#74777f'
  outline-variant: '#c4c6cf'
  surface-tint: '#435f89'
  primary: '#002043'
  on-primary: '#ffffff'
  primary-container: '#17365d'
  on-primary-container: '#84a0cd'
  inverse-primary: '#acc8f7'
  secondary: '#0051d5'
  on-secondary: '#ffffff'
  secondary-container: '#316bf3'
  on-secondary-container: '#fefcff'
  tertiary: '#00270b'
  on-tertiary: '#ffffff'
  tertiary-container: '#003f17'
  on-tertiary-container: '#32b559'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#d5e3ff'
  primary-fixed-dim: '#acc8f7'
  on-primary-fixed: '#001c3b'
  on-primary-fixed-variant: '#2b486f'
  secondary-fixed: '#dbe1ff'
  secondary-fixed-dim: '#b4c5ff'
  on-secondary-fixed: '#00174b'
  on-secondary-fixed-variant: '#003ea8'
  tertiary-fixed: '#7ffc97'
  tertiary-fixed-dim: '#62df7d'
  on-tertiary-fixed: '#002109'
  on-tertiary-fixed-variant: '#005320'
  background: '#f8f9ff'
  on-background: '#0b1c30'
  surface-variant: '#d3e4fe'
typography:
  headline-xl:
    fontFamily: Inter
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 36px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Inter
    fontSize: 22px
    fontWeight: '600'
    lineHeight: 28px
    letterSpacing: -0.015em
  headline-md:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 24px
    letterSpacing: -0.01em
  title-sm:
    fontFamily: Inter
    fontSize: 15px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: -0.005em
  body-default:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
    letterSpacing: '0'
  body-medium:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '500'
    lineHeight: 20px
    letterSpacing: '0'
  body-sm:
    fontFamily: Inter
    fontSize: 13px
    fontWeight: '400'
    lineHeight: 18px
    letterSpacing: '0'
  label-default:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.01em
  label-uppercase:
    fontFamily: Inter
    fontSize: 11px
    fontWeight: '600'
    lineHeight: 14px
    letterSpacing: 0.05em
  code-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: '0'
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  gutter: 1rem
  gutter-lg: 1.5rem
  margin: 1.5rem
  margin-compact: 1rem
  space-xxs: 0.125rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 0.75rem
  space-lg: 1rem
  space-xl: 1.5rem
  space-2xl: 2rem
---

## Brand & Style
This design system governs the administrative, academic, and collaborative task ecosystem for Universitas Pembangunan Nasional "Veteran" Yogyakarta. It delivers the rigor, speed, and precision of tier-one enterprise engineering tools (such as Linear and Asana) while maintaining institutional authority and accessibility for Indonesian higher-education governance.

The aesthetic philosophy centers on:
- **Utilitarian Elegance**: Eliminating ornamental fluff in favor of high-density information architecture, crisp boundaries, and immediate scannability.
- **Institutional Dignity**: Rooted in deep national campus navy, paired with precise dynamic status indicators that prevent cognitive overload across cross-faculty and institutional workflows.
- **Focus and Ergonomics**: Low-contrast structural scaffolding that disappears into the background, elevating actionable project data, task statuses, and institutional KPIs.

## Colors
The color palette prioritizes spatial hierarchy, semantic clarity, and high data density.

### Core Roles
- **Primary Brand (`#17365D`)**: Used for institutional framing, persistent navigation bars, primary headers, and high-level enterprise context.
- **Interactive Accent (`#2563EB`)**: Applied strictly to clickable states, selected tabs, progress fills, focused borders, and primary calls-to-action.
- **Background Canvas (`#F8FAFC`)**: A cool-tinted, low-glare surface that reduces ocular strain during prolonged desktop data entry.
- **Surface & Panel (`#FFFFFF`)**: Pure neutral white dedicated to task boards, modals, data tables, and elevated dropdown cards.
- **Structural Border (`#E2E8F0`)**: Crisp, low-contrast demarcation for dividers, table rows, and card perimeters without visual noise.

### Semantic & Text Spectrum
- **Text Headings (`#0F172A`)**: Maximum readability for task titles and high-level metrics.
- **Secondary Text (`#64748B`)**: Metadata, table headers, breadcrumbs, and timestamp strings.
- **Success (`#16A34A`)**: Completed deliverables, verified approvals, and on-track milestones.
- **Warning (`#D97706`)**: Pending faculty reviews, approaching deadlines, and bottleneck warnings.
- **Danger / Overdue (`#DC2626`)**: Critical blockers, missed academic deadlines, and high-severity issues.

## Typography
This typography scale leverages Inter to maximize legibility across dense tabular views, Kanban boards, and multi-layered drawer panels.

- **Weight Discipline**: Restrict font weights strictly to Regular (400), Medium (500), Semi-Bold (600), and Bold (700). Avoid ultra-thin or ultra-black weights.
- **Tabular Numerals**: Numeric figures across task IDs, dates, progress counters, and financial/credit unit figures must use `font-variant-numeric: tabular-nums` to maintain vertical alignment in data tables.
- **Labels & Micro-copy**: Use `label-uppercase` for table column headers, filter group tags, and system badges to establish clear distinction from content items.

## Layout & Spacing
The layout adheres to a strict desktop-first 8pt/4pt layout grid engineered for complex administrative applications.

- **Application Shell**: Built on a three-tier layout consisting of a fixed navigation sidebar (width: 240px or 64px collapsed), a situational context panel (width: 280px for filter/views), and a fluid workspace pane.
- **Data Densities**:
  - *Standard*: `space-md` (12px) cell padding for standard project overviews and settings.
  - *Compact*: `space-sm` (8px) cell padding for dense sprint tables, spreadsheet list views, and nested subtasks.
- **Alignment Guidelines**: Form inputs, action toolbars, and list item heights must resolve strictly to multiples of 4px (e.g., standard input height: 36px, compact task row: 32px, button height: 36px).

## Elevation & Depth
Depth is produced through subtle borders and restrained micro-shadows rather than high-contrast drops, retaining a clean SaaS architectural discipline.

- **Level 0 (Flat Canvas)**: Canvas `#F8FAFC`, borders `#E2E8F0`. No shadow.
- **Level 1 (Card / Grid Tile)**: Surface `#FFFFFF`, border 1px solid `#E2E8F0`, shadow: `0 1px 2px 0 rgba(15, 23, 42, 0.04)`.
- **Level 2 (Hover State / Floating Toolbar)**: Surface `#FFFFFF`, border 1px solid `#CBD5E1`, shadow: `0 4px 6px -1px rgba(15, 23, 42, 0.07), 0 2px 4px -2px rgba(15, 23, 42, 0.05)`.
- **Level 3 (Dropdowns / Flyout Menus)**: Surface `#FFFFFF`, border 1px solid `#E2E8F0`, shadow: `0 10px 15px -3px rgba(15, 23, 42, 0.08), 0 4px 6px -4px rgba(15, 23, 42, 0.03)`.
- **Level 4 (Modal Dialogs / Global Search)**: Surface `#FFFFFF`, border 1px solid `#CBD5E1`, shadow: `0 20px 25px -5px rgba(15, 23, 42, 0.1), 0 8px 10px -6px rgba(15, 23, 42, 0.04)`. Accompanied by a 20% opacity neutral backdrop blur overlay (`rgba(15, 23, 42, 0.35)`).

## Shapes
The system utilizes a structured, soft corner language (`roundedness: 1`), conveying enterprise efficiency and clean architectural lines without playful over-rounding.

- **Base Radius (`0.25rem` / 4px)**: Applied to checkboxes, micro badges, table hover indicators, and embedded metadata tokens.
- **Default Container Radius (`0.375rem` / 6px)**: Applied to buttons, text inputs, dropdown selections, and tab pills.
- **Panel Radius (`0.5rem` / 8px)**: Applied to task cards, modal dialogs, flyout drawers, and segmented content blocks.
- **Full Radius (`9999px`)**: Reserved exclusively for user avatars, status dot indicators, and live activity pings.

## Components

### Buttons & Interactive Controls
- **Primary**: Background `#17365D`, text `#FFFFFF`, border none. Hover state `#1E4273`. Active state `#122B4A`.
- **Accent**: Background `#2563EB`, text `#FFFFFF`, hover `#1D4ED8`. Used for direct project creation and action execution.
- **Secondary / Outline**: Background `#FFFFFF`, text `#0F172A`, border 1px solid `#E2E8F0`. Hover state `#F1F5F9` with border `#CBD5E1`.
- **Ghost**: Background transparent, text `#64748B`, hover background `#F1F5F9`, hover text `#0F172A`.

### Task Status Chips & Badges
- Displayed with subtle tinted backgrounds and solid foreground text (height: 22px, padding: 2px 8px, font-size: 11px, weight: 600).
  - *Not Started / Backlog*: Background `#F1F5F9`, text `#475569`.
  - *In Progress*: Background `#EFF6FF`, text `#2563EB`.
  - *In Review*: Background `#FEF3C7`, text `#D97706`.
  - *Done*: Background `#DCFCE7`, text `#16A34A`.
  - *Blocked / Overdue*: Background `#FEE2E2`, text `#DC2626`.

### Inputs & Select Fields
- Height: 36px (standard desktop entry).
- Surface: `#FFFFFF`, border 1px solid `#E2E8F0`, corner radius 6px, text `#0F172A`.
- Focus: Border color `#2563EB`, subtle focus ring: `0 0 0 3px rgba(37, 99, 235, 0.15)`.
- Placeholder: Text color `#94A3B8`.

### Checkboxes & Selectors
- Size: 16px × 16px. Corner radius: 4px.
- Unchecked: Border 1px solid `#CBD5E1`, background `#FFFFFF`.
- Checked: Background `#2563EB`, border `#2563EB`, checkmark `#FFFFFF`.

### Task Cards & Board Columns
- **Cards**: Border 1px solid `#E2E8F0`, surface `#FFFFFF`, padding `space-md` (12px). Hover reveals draggable handles and elevates to Level 2.
- **Kanban Columns**: Background `#F1F5F9`, border 1px solid `#E2E8F0`, corner radius 8px, header counter chip displaying total task volume.

### Specialized Enterprise Components
- **Command Palette (`Ctrl/Cmd + K`)**: Centered 640px overlay panel, quick navigation between faculties, projects, and personal assignees.
- **Data Table Row**: Border bottom 1px solid `#E2E8F0`, hover background `#F8FAFC`, integrated assignee avatar stack (24px overlapping avatars), and inline priority flag picker.
