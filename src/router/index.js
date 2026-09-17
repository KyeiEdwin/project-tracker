import { createRouter, createWebHistory } from 'vue-router'

// Views - Dashboard
import Dashboard from '@/views/Dashboard.vue'

// Views - Projects
import ProjectsList from '@/views/projects/ProjectsList.vue'
import ProjectCreate from '@/views/projects/ProjectCreate.vue'
import ProjectDetails from '@/views/projects/ProjectDetails.vue'

// Views - Initiation
import Kickoff from '@/views/initiation/Kickoff.vue'
import Stakeholders from '@/views/initiation/Stakeholders.vue'

// Views - Agile
import Sprints from '@/views/agile/Sprints.vue'
import Backlog from '@/views/agile/Backlog.vue'
import AgileDefinitions from '@/views/agile/AgileDefinitions.vue'

// Views - Tasks
import TasksList from '@/views/tasks/TasksList.vue'
import TasksKanban from '@/views/tasks/TasksKanban.vue'
import Workflows from '@/views/tasks/Workflows.vue'

// Views - Resources
import Resources from '@/views/resources/Resources.vue'
import TimeTracking from '@/views/resources/TimeTracking.vue'
import Budget from '@/views/resources/Budget.vue'
import Milestones from '@/views/resources/Milestones.vue'
import ProjectGantt from '@/views/resources/ProjectGantt.vue'

// Views - Quality
import QaTesting from '@/views/quality/QaTesting.vue'
import ProjectRisks from '@/views/quality/ProjectRisks.vue'
import ChangeLog from '@/views/quality/ChangeLog.vue'

// Views - Reports
import Reports from '@/views/reports/Reports.vue'
import Documents from '@/views/reports/Documents.vue'
import LessonsLearned from '@/views/reports/LessonsLearned.vue'

// Views - Communication
import ProjectChat from '@/views/communication/ProjectChat.vue'

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: Dashboard,
    meta: { title: 'Dashboard', breadcrumb: 'Dashboard' }
  },
  // Projects
  {
    path: '/projects',
    name: 'ProjectsList',
    component: ProjectsList,
    meta: { title: 'Projects List', breadcrumb: 'Projects' }
  },
  {
    path: '/projects/create',
    name: 'ProjectCreate',
    component: ProjectCreate,
    meta: { title: 'Create Project', breadcrumb: 'Create Project' }
  },
  {
    path: '/projects/:id',
    name: 'ProjectDetails',
    component: ProjectDetails,
    meta: { title: 'Project Details', breadcrumb: 'Project Details' }
  },
  // Initiation
  {
    path: '/initiation/kickoff',
    name: 'Kickoff',
    component: Kickoff,
    meta: { title: 'Project Kick-Off', breadcrumb: 'Kick-Off' }
  },
  {
    path: '/initiation/stakeholders',
    name: 'Stakeholders',
    component: Stakeholders,
    meta: { title: 'Stakeholders', breadcrumb: 'Stakeholders' }
  },
  // Agile
  {
    path: '/agile/sprints',
    name: 'Sprints',
    component: Sprints,
    meta: { title: 'Sprints', breadcrumb: 'Sprints' }
  },
  {
    path: '/agile/backlog',
    name: 'Backlog',
    component: Backlog,
    meta: { title: 'Backlog', breadcrumb: 'Backlog' }
  },
  {
    path: '/agile/definitions',
    name: 'AgileDefinitions',
    component: AgileDefinitions,
    meta: { title: 'DoR / DoD', breadcrumb: 'Definitions' }
  },
  // Tasks
  {
    path: '/tasks',
    name: 'TasksList',
    component: TasksList,
    meta: { title: 'Task List', breadcrumb: 'Tasks' }
  },
  {
    path: '/tasks/kanban',
    name: 'TasksKanban',
    component: TasksKanban,
    meta: { title: 'Kanban Board', breadcrumb: 'Kanban' }
  },
  {
    path: '/tasks/workflows',
    name: 'Workflows',
    component: Workflows,
    meta: { title: 'Workflows', breadcrumb: 'Workflows' }
  },
  // Resources
  {
    path: '/resources/team',
    name: 'Resources',
    component: Resources,
    meta: { title: 'Team Resources', breadcrumb: 'Team' }
  },
  {
    path: '/resources/time-tracking',
    name: 'TimeTracking',
    component: TimeTracking,
    meta: { title: 'Time Tracking', breadcrumb: 'Time Tracking' }
  },
  {
    path: '/resources/budget',
    name: 'Budget',
    component: Budget,
    meta: { title: 'Budget', breadcrumb: 'Budget' }
  },
  {
    path: '/resources/milestones',
    name: 'Milestones',
    component: Milestones,
    meta: { title: 'Milestones', breadcrumb: 'Milestones' }
  },
  {
    path: '/resources/gantt',
    name: 'ProjectGantt',
    component: ProjectGantt,
    meta: { title: 'Gantt Chart', breadcrumb: 'Gantt' }
  },
  // Quality
  {
    path: '/quality/qa-testing',
    name: 'QaTesting',
    component: QaTesting,
    meta: { title: 'QA & Testing', breadcrumb: 'QA Testing' }
  },
  {
    path: '/quality/risks',
    name: 'ProjectRisks',
    component: ProjectRisks,
    meta: { title: 'Risks & Issues', breadcrumb: 'Risks' }
  },
  {
    path: '/quality/change-log',
    name: 'ChangeLog',
    component: ChangeLog,
    meta: { title: 'Change Log', breadcrumb: 'Change Log' }
  },
  // Reports
  {
    path: '/reports/analytics',
    name: 'Reports',
    component: Reports,
    meta: { title: 'Reports & Analytics', breadcrumb: 'Analytics' }
  },
  {
    path: '/reports/documents',
    name: 'Documents',
    component: Documents,
    meta: { title: 'Documents', breadcrumb: 'Documents' }
  },
  {
    path: '/reports/lessons-learned',
    name: 'LessonsLearned',
    component: LessonsLearned,
    meta: { title: 'Lessons Learned', breadcrumb: 'Lessons' }
  },
  // Communication
  {
    path: '/chat',
    name: 'ProjectChat',
    component: ProjectChat,
    meta: { title: 'Project Chat', breadcrumb: 'Chat' }
  },
  // Catch-all redirect
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

// Update page title on navigation
router.beforeEach((to, from, next) => {
  document.title = `Project Tracker - ${to.meta.title || 'Dashboard'}`
  next()
})

export default router

