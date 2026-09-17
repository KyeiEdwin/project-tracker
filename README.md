# Project Tracker - Vue.js

A comprehensive project management application built with Vue 3, Vite, and the Xintra template design system.

## Features

- **Dashboard**: Overview of projects, tasks, and key metrics
- **Projects**: Create, manage, and track project progress
- **Sprints & Agile**: Sprint planning, backlog management, and agile workflows
- **Tasks**: Task lists, Kanban boards, and workflow management
- **Resources**: Team management, time tracking, budget, milestones, and Gantt charts
- **Quality**: QA testing, risk management, and change control
- **Reports**: Analytics, documents, and lessons learned
- **Chat**: Real-time team communication

## Tech Stack

- **Vue 3** - Progressive JavaScript framework
- **Vue Router** - Official router for Vue.js
- **Pinia** - State management
- **Vite** - Next-generation build tool
- **Tailwind CSS** - Utility-first CSS framework
- **Xintra Design System** - Admin template components

## Getting Started

### Prerequisites

- Node.js 18+ 
- npm or yarn

### Installation

```bash
# Navigate to the vue-app directory
cd vue-app

# Install dependencies
npm install

# Start development server
npm run dev
```

### Build for Production

```bash
npm run build
```

### Preview Production Build

```bash
npm run preview
```

## Project Structure

```
vue-app/
├── src/
│   ├── assets/
│   │   ├── css/           # Stylesheets
│   │   └── images/        # Images and icons
│   ├── components/
│   │   ├── layout/        # Header, Sidebar, Footer
│   │   └── ui/            # Reusable UI components
│   ├── views/
│   │   ├── projects/      # Project management pages
│   │   ├── tasks/         # Task management pages
│   │   ├── agile/         # Agile/Sprint pages
│   │   ├── initiation/    # Project initiation pages
│   │   ├── resources/     # Resource management pages
│   │   ├── quality/       # QA and quality pages
│   │   ├── reports/       # Reports and documents
│   │   └── communication/ # Chat and messaging
│   ├── router/            # Vue Router configuration
│   ├── App.vue            # Root component
│   └── main.js            # Application entry point
├── public/                # Static assets
├── index.html             # HTML entry point
├── package.json           # Dependencies
└── vite.config.js         # Vite configuration
```

## Available Routes

| Route | Component | Description |
|-------|-----------|-------------|
| `/` | Dashboard | Main dashboard |
| `/projects` | ProjectsList | All projects |
| `/projects/create` | ProjectCreate | Create new project |
| `/projects/:id` | ProjectDetails | Project details |
| `/initiation/kickoff` | Kickoff | Project kickoff |
| `/initiation/stakeholders` | Stakeholders | Stakeholder management |
| `/agile/sprints` | Sprints | Sprint management |
| `/agile/backlog` | Backlog | Product backlog |
| `/agile/definitions` | AgileDefinitions | DoR/DoD framework |
| `/tasks` | TasksList | Task list |
| `/tasks/kanban` | TasksKanban | Kanban board |
| `/tasks/workflows` | Workflows | Workflow management |
| `/resources/team` | Resources | Team management |
| `/resources/time-tracking` | TimeTracking | Time tracking |
| `/resources/budget` | Budget | Budget management |
| `/resources/milestones` | Milestones | Project milestones |
| `/resources/gantt` | ProjectGantt | Gantt chart |
| `/quality/qa-testing` | QaTesting | QA & testing |
| `/quality/risks` | ProjectRisks | Risk management |
| `/quality/change-log` | ChangeLog | Change requests |
| `/reports/analytics` | Reports | Reports & analytics |
| `/reports/documents` | Documents | Document management |
| `/reports/lessons-learned` | LessonsLearned | Lessons learned |
| `/chat` | ProjectChat | Team chat |

## Customization

### Theming

The app supports light and dark themes. Modify theme settings in `App.vue`:

```javascript
document.documentElement.setAttribute('data-nav-layout', 'horizontal')
document.documentElement.setAttribute('data-menu-styles', 'light')
document.documentElement.setAttribute('data-header-styles', 'light')
```

### Adding New Pages

1. Create a new component in `src/views/`
2. Add the route in `src/router/index.js`
3. Update the navigation in `src/components/layout/AppSidebar.vue`

## License

MIT

