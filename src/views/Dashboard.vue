<script setup>
import { onMounted, nextTick } from 'vue'
import ApexCharts from 'apexcharts'
import PageHeader from '@/components/ui/PageHeader.vue'

let charts = []

onMounted(() => {
  nextTick(() => {
    initializeCharts()
  })
})

const initializeCharts = () => {
  // Get primary color from CSS variable
  const getPrimaryColor = () => {
    const root = document.documentElement
    const primaryRgb = getComputedStyle(root).getPropertyValue('--primary-rgb').trim()
    return primaryRgb ? `rgb(${primaryRgb})` : 'rgb(92, 103, 247)' // fallback
  }

  const primaryColor = getPrimaryColor()

  // Sparkline charts for KPI cards (Projects-1, Projects-2, Projects-3, Projects-4)
  const sparklineOptions = [
    {
      id: 'Projects-1',
      color: primaryColor
    },
    {
      id: 'Projects-2',
      color: 'rgb(227, 84, 212)'
    },
    {
      id: 'Projects-3',
      color: 'rgb(255, 93, 159)'
    },
    {
      id: 'Projects-4',
      color: 'rgb(255, 142, 111)'
    }
  ]

  sparklineOptions.forEach((config) => {
    const element = document.querySelector(`#${config.id}`)
    if (element) {
      const options = {
        series: [
          {
            data: [12, 14, 18, 47, 42, 15, 47, 75, 65, 19, 14, 50]
          }
        ],
        chart: {
          type: 'bar',
          width: 70,
          height: 40,
          sparkline: {
            enabled: true
          }
        },
        plotOptions: {
          bar: {
            columnWidth: '80%',
            borderRadius: 2
          }
        },
        stroke: {
          curve: 'smooth',
          width: 2
        },
        labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
        colors: [config.color],
        tooltip: {
          fixed: {
            enabled: false
          },
          x: {
            show: false
          },
          y: {
            title: {
              formatter: function () {
                return ''
              }
            }
          }
        }
      }
      const chart = new ApexCharts(element, options)
      chart.render()
      charts.push(chart)
    }
  })

  // Project Statistics Chart (mixed area/bar chart)
  const projectStatsElement = document.querySelector('#project-statistics')
  if (projectStatsElement) {
    const projectStatsOptions = {
      series: [
        {
          name: 'Projects',
          type: 'area',
          data: [15, 28, 23, 23, 41, 58, 48, 50, 22, 31, 40, 45]
        },
        {
          name: 'Revenue',
          type: 'bar',
          data: [20, 29, 37, 35, 44, 43, 50, 20, 20, 45, 45, 52]
        }
      ],
      chart: {
        type: 'area',
        height: 360,
        animations: {
          speed: 500
        },
        toolbar: {
          show: false
        },
        dropShadow: {
          enabled: true,
          enabledOnSeries: undefined,
          top: 8,
          left: 0,
          blur: 4,
          color: '#000',
          opacity: 0.08
        }
      },
      colors: ['rgb(227, 84, 212)', primaryColor],
      dataLabels: {
        enabled: false
      },
      grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 3
      },
      fill: {
        type: ['gradient', 'solid'],
        gradient: {
          opacityFrom: 0.1,
          opacityTo: 0.2,
          shadeIntensity: 0.1
        }
      },
      stroke: {
        curve: ['smooth', 'smooth'],
        width: [2, 1.5],
        dashArray: [4, 5]
      },
      xaxis: {
        axisTicks: {
          show: false
        }
      },
      yaxis: {
        labels: {
          formatter: function (value) {
            return value
          }
        }
      },
      legend: {
        show: true,
        position: 'bottom',
        inverseOrder: true
      },
      plotOptions: {
        bar: {
          columnWidth: '20%',
          borderRadius: 3,
          borderRadiusApplication: 'end',
          borderRadiusWhenStacked: 'last'
        }
      }
    }
    const projectStatsChart = new ApexCharts(projectStatsElement, projectStatsOptions)
    projectStatsChart.render()
    charts.push(projectStatsChart)
  }

  // Monthly Target Chart (radial bar)
  const monthlyTargetElement = document.querySelector('#monthly-target')
  if (monthlyTargetElement) {
    const monthlyTargetOptions = {
      series: [86, 80, 60],
      chart: {
        height: 260,
        type: 'radialBar'
      },
      plotOptions: {
        radialBar: {
          dataLabels: {
            name: {
              fontSize: '22px',
              offsetY: 0
            },
            value: {
              fontSize: '14px',
              offsetY: 5
            },
            total: {
              show: true,
              label: 'Total',
              formatter: function () {
                return 249
              }
            }
          }
        }
      },
      stroke: {
        lineCap: 'round'
      },
      grid: {
        padding: {
          bottom: -10,
          top: -10
        }
      },
      colors: [primaryColor, 'rgba(227, 84, 212, 0.5)', 'rgba(255, 93, 159, 0.4)'],
      labels: ['New Projects', 'Completed', 'Pending']
    }
    const monthlyTargetChart = new ApexCharts(monthlyTargetElement, monthlyTargetOptions)
    monthlyTargetChart.render()
    charts.push(monthlyTargetChart)
  }

  // Tasks Report Chart (line chart)
  const tasksReportElement = document.querySelector('#tasks-report')
  if (tasksReportElement) {
    const tasksReportOptions = {
      series: [
        {
          name: 'This Week',
          data: [44, 42, 57, 86, 58, 55, 70]
        },
        {
          name: 'Last Week',
          data: [34, 22, 42, 56, 21, 86, 60]
        }
      ],
      chart: {
        type: 'line',
        height: 340,
        toolbar: {
          show: false
        }
      },
      grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 3
      },
      stroke: {
        width: 2,
        curve: 'smooth',
        dashArray: [0, 3]
      },
      colors: [primaryColor, 'rgb(227, 84, 212)'],
      plotOptions: {
        bar: {
          borderRadius: 2,
          colors: {
            ranges: [
              {
                from: -100,
                to: -46,
                color: '#ebeff5'
              },
              {
                from: -45,
                to: 0,
                color: '#ebeff5'
              }
            ]
          },
          columnWidth: '50%',
          dropShadow: {
            enabled: true,
            color: '#000',
            top: 1,
            left: 1,
            blur: 2,
            opacity: 0.5
          }
        }
      },
      dataLabels: {
        enabled: false
      },
      legend: {
        show: true,
        position: 'top'
      },
      tooltip: {
        enabled: true,
        theme: 'dark'
      },
      yaxis: {
        title: {
          style: {
            color: '#adb5be',
            fontSize: '14px',
            fontFamily: 'poppins, sans-serif',
            fontWeight: 600,
            cssClass: 'apexcharts-yaxis-label'
          }
        },
        labels: {
          formatter: function (y) {
            if (y === null || y === undefined) {
              return '0'
            }
            return y.toFixed(0) + ''
          }
        }
      },
      xaxis: {
        type: 'category',
        categories: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
        axisBorder: {
          show: true,
          color: 'rgba(119, 119, 142, 0.05)',
          offsetX: 0,
          offsetY: 0
        },
        axisTicks: {
          show: true,
          borderType: 'solid',
          color: 'rgba(119, 119, 142, 0.05)',
          width: 6,
          offsetX: 0,
          offsetY: 0
        },
        labels: {
          rotate: -90
        }
      }
    }
    const tasksReportChart = new ApexCharts(tasksReportElement, tasksReportOptions)
    tasksReportChart.render()
    charts.push(tasksReportChart)
  }
}
</script>

<template>
  <div>
    <!-- Projects Dashboard Header -->
    <PageHeader title="Projects" subtitle="Portfolio overview and performance" />

    <!-- Row 1: Banner & Team -->
    <div class="grid grid-cols-12 gap-x-6">
      <!-- Banner -->
      <div class="xxl:col-span-5 col-span-12">
        <div class="box main-dashboard-banner project-dashboard-banner overflow-hidden">
          <div class="box-body p-[1.5rem]">
            <div class="grid grid-cols-12 gap-x-6 justify-between">
              <div class="xxl:col-span-8 xl:col-span-5 lg:col-span-5 md:col-span-5 sm:col-span-5 col-span-12">
                <h4 class="mb-1 font-medium text-white">Manage Projects</h4>
                <p class="mb-3 text-white opacity-70">
                  Manage projects effortlessly with our one-click solution, streamlining your workflow.
                </p>
                <router-link class="ti-btn ti-btn-sm bg-primarytint1color text-white" to="/projects">
                  Manage Now<i class="ti ti-arrow-narrow-right ms-1"></i>
                </router-link>
              </div>
              <div
                class="xxl:col-span-4 xl:col-span-7 lg:col-span-7 md:col-span-7 sm:col-span-7 col-span-12 sm:block hidden text-end my-auto"
              >
                <img alt="Projects illustration" class="img-fluid" src="@/assets/images/media-85.png" />
              </div>
            </div>
          </div>
        </div>

        <!-- Team snapshot -->
        <div class="box overflow-hidden">
          <div class="box-header justify-between">
            <div class="box-title">Team</div>
            <a class="ti-btn ti-btn-sm bg-light" href="javascript:void(0);">View All</a>
          </div>
          <div class="box-body p-0">
            <div class="table-responsive">
              <table class="table table-hover whitespace-nowrap">
                <thead>
                  <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                    <th scope="col">Name</th>
                    <th scope="col">Works</th>
                    <th scope="col">Status</th>
                    <th scope="col">Tasks</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody class="top-selling">
                  <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                    <td>
                      <div class="flex">
                        <span class="avatar avatar-sm avatar-rounded">
                          <img alt="" class="" src="@/assets/images/2.jpg" />
                        </span>
                        <div class="flex-1 ms-2">
                          <span class="block font-semibold">Richard Dom</span>
                          <a class="text-textmuted dark:text-textmuted/50 text-xs" href="javascript:void(0);">Team Leader</a>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="font-medium">457</span>
                    </td>
                    <td>
                      <span class="badge leading-none bg-success/10 text-success">Online</span>
                    </td>
                    <td>
                      <span class="">564/ <span class="text-textmuted dark:text-textmuted/50">1145</span></span>
                    </td>
                    <td>
                      <div class="flex items-center gap-2">
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-2 ti-btn-soft-primary !m-0"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-user-plus align-middle"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              Assign
                            </span>
                          </a>
                        </div>
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-0 ti-btn-soft-info"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-at align-middle"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              Mail
                            </span>
                          </a>
                        </div>
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-2 ti-btn-soft-primary2 !m-02"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-eye align-middle"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              View
                            </span>
                          </a>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                    <td>
                      <div class="flex">
                        <span class="avatar avatar-sm avatar-rounded">
                          <img alt="" class="" src="@/assets/images/3.jpg" />
                        </span>
                        <div class="flex-1 ms-2">
                          <span class="block font-semibold">Nikki Jey</span>
                          <a class="text-textmuted dark:text-textmuted/50 text-xs" href="javascript:void(0);">UI Developer</a>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="font-medium">647</span>
                    </td>
                    <td>
                      <span class="badge leading-none bg-danger/10 text-danger">Offline</span>
                    </td>
                    <td>
                      <span class="">631/ <span class="text-textmuted dark:text-textmuted/50">1145</span></span>
                    </td>
                    <td>
                      <div class="flex items-center gap-2">
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-2 ti-btn-soft-primary !m-0"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-user-plus align-middle"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              Assign
                            </span>
                          </a>
                        </div>
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-0 ti-btn-soft-info"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-at align-middle"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              Mail
                            </span>
                          </a>
                        </div>
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-2 ti-btn-soft-primary2 !m-02"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-eye align-middle"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              View
                            </span>
                          </a>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                    <td>
                      <div class="flex">
                        <span class="avatar avatar-sm avatar-rounded">
                          <img alt="" class="" src="@/assets/images/21.jpg" />
                        </span>
                        <div class="flex-1 ms-2">
                          <span class="block font-semibold">Arifa Zed</span>
                          <a class="text-textmuted dark:text-textmuted/50 text-xs" href="javascript:void(0);">Web Developer</a>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="font-medium">983</span>
                    </td>
                    <td>
                      <span class="badge leading-none bg-success/10 text-success">Online</span>
                    </td>
                    <td>
                      <span class="">502/ <span class="text-textmuted dark:text-textmuted/50">1236</span></span>
                    </td>
                    <td>
                      <div class="flex items-center gap-2">
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-2 ti-btn-soft-primary !m-0"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-user-plus align-middle"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              Assign
                            </span>
                          </a>
                        </div>
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-0 ti-btn-soft-info"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-at align-middle"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              Mail
                            </span>
                          </a>
                        </div>
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-2 ti-btn-soft-primary2 !m-02"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-eye align-middle"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              View
                            </span>
                          </a>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                    <td>
                      <div class="flex">
                        <span class="avatar avatar-sm avatar-rounded">
                          <img alt="" class="" src="@/assets/images/4.jpg" />
                        </span>
                        <div class="flex-1 ms-2">
                          <span class="block font-semibold">Xiong Yu</span>
                          <a class="text-textmuted dark:text-textmuted/50 text-xs" href="javascript:void(0);">Team Member</a>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="font-medium">631</span>
                    </td>
                    <td>
                      <span class="badge leading-none bg-success/10 text-success">Online</span>
                    </td>
                    <td>
                      <span class="">360/ <span class="text-textmuted dark:text-textmuted/50">457</span></span>
                    </td>
                    <td>
                      <div class="flex items-center gap-2">
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-2 ti-btn-soft-primary !m-0"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-user-plus align-middle"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              Assign
                            </span>
                          </a>
                        </div>
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-0 ti-btn-soft-info"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-at align-middle"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              Mail
                            </span>
                          </a>
                        </div>
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-2 ti-btn-soft-primary2 !m-02"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-eye align-middle"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              View
                            </span>
                          </a>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                    <td class="border-b-0">
                      <div class="flex">
                        <span class="avatar avatar-sm avatar-rounded">
                          <img alt="" class="" src="@/assets/images/11.jpg" />
                        </span>
                        <div class="flex-1 ms-2">
                          <span class="block font-semibold">Emanuel Gen</span>
                          <a class="text-textmuted dark:text-textmuted/50 text-xs" href="javascript:void(0);">Project Manager</a>
                        </div>
                      </div>
                    </td>
                    <td class="border-b-0">
                      <span class="font-medium">478</span>
                    </td>
                    <td class="border-b-0">
                      <span class="badge leading-none bg-danger/10 text-danger">Offline</span>
                    </td>
                    <td class="border-b-0">
                      <span class="">558/ <span class="text-textmuted dark:text-textmuted/50">698</span></span>
                    </td>
                    <td class="border-b-0">
                      <div class="flex items-center gap-2">
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-2 ti-btn-soft-primary !m-0"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-user-plus align-middle"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              Assign
                            </span>
                          </a>
                        </div>
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-0 ti-btn-soft-info"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-at align-middle"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              Mail
                            </span>
                          </a>
                        </div>
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-2 ti-btn-soft-primary2 !m-02"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-eye align-middle"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              View
                            </span>
                          </a>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- KPI Cards & Project Statistics -->
      <div class="xxl:col-span-7 col-span-12">
        <div class="grid grid-cols-12 gap-x-6">
          <!-- New Projects -->
          <div class="xxl:col-span-3 md:col-span-6 col-span-12">
            <div class="box overflow-hidden">
              <div class="box-body">
                <div class="mb-5 flex items-start justify-between">
                  <span class="avatar avatar-sm bg-primary svg-white">
                    <i class="ri-pages-line text-[1rem]"></i>
                  </span>
                  <span class="badge leading-none bg-danger/10 text-danger">-5.20%</span>
                </div>
                <div class="flex align-items-end justify-between flex-wrap">
                  <div class="flex-shrink-0 leading-none">
                    <div class="text-textmuted dark:text-textmuted/50 mb-2">New Projects</div>
                    <h4 class="mb-0 text-xl font-medium">432</h4>
                  </div>
                  <div class="flex-shrink-0 text-end ms-auto" id="Projects-2"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Completed -->
          <div class="xxl:col-span-3 md:col-span-6 col-span-12">
            <div class="box overflow-hidden">
              <div class="box-body">
                <div class="mb-5 flex items-start justify-between">
                  <span class="avatar avatar-sm bg-primarytint1color svg-white">
                    <i class="ri-check-double-line text-[1rem]"></i>
                  </span>
                  <span class="badge leading-none bg-success/10 text-success">+7.20%</span>
                </div>
                <div class="flex align-items-end justify-between flex-wrap">
                  <div class="flex-shrink-0 leading-none">
                    <div class="text-textmuted dark:text-textmuted/50 mb-2">Completed</div>
                    <h4 class="mb-0 text-xl font-medium">122</h4>
                  </div>
                  <div class="flex-shrink-0 text-end ms-auto" id="Projects-1"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Ongoing Projects -->
          <div class="xxl:col-span-3 md:col-span-6 col-span-12">
            <div class="box overflow-hidden">
              <div class="box-body">
                <div class="mb-5 flex items-start justify-between">
                  <span class="avatar avatar-sm bg-primarytint2color svg-white">
                    <i class="ri-loop-left-fill text-[1rem]"></i>
                  </span>
                  <span class="badge leading-none bg-danger/10 text-danger">-5.20%</span>
                </div>
                <div class="flex align-items-end justify-between flex-wrap">
                  <div class="flex-shrink-0 leading-none">
                    <div class="text-textmuted dark:text-textmuted/50 mb-2">Ongoing Projects</div>
                    <h4 class="mb-0 text-xl font-medium">1,265</h4>
                  </div>
                  <div class="flex-shrink-0 text-end ms-auto" id="Projects-3"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pending Projects -->
          <div class="xxl:col-span-3 md:col-span-6 col-span-12">
            <div class="box overflow-hidden">
              <div class="box-body">
                <div class="mb-5 flex items-start justify-between">
                  <span class="avatar avatar-sm bg-primarytint3color svg-white">
                    <i class="ri-time-line text-[1rem]"></i>
                  </span>
                  <span class="badge leading-none bg-success/10 text-success">+5.20%</span>
                </div>
                <div class="flex align-items-end justify-between flex-wrap">
                  <div class="flex-shrink-0 leading-none">
                    <div class="text-textmuted dark:text-textmuted/50 mb-2">Pending Projects</div>
                    <h4 class="mb-0 text-xl font-medium">1,265</h4>
                  </div>
                  <div class="flex-shrink-0 text-end ms-auto" id="Projects-4"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Project Statistics summary -->
        <div class="box mt-6">
          <div class="box-header justify-between">
            <div class="box-title">Project Statistics</div>
            <div class="ti-dropdown hs-dropdown">
              <a
                aria-expanded="false"
                aria-label="anchor"
                class="ti-btn ti-btn-sm bg-light"
                data-bs-toggle="dropdown"
                href="javascript:void(0);"
              >
                Last Week <i class="ri-arrow-down-s-line align-middle inline-block"></i>
              </a>
              <ul class="ti-dropdown-menu hs-dropdown-menu hidden">
                <li><a class="ti-dropdown-item" href="javascript:void(0);">Today</a></li>
                <li><a class="ti-dropdown-item" href="javascript:void(0);">Last Week</a></li>
                <li><a class="ti-dropdown-item" href="javascript:void(0);">Last Month</a></li>
                <li><a class="ti-dropdown-item" href="javascript:void(0);">Last Year</a></li>
              </ul>
            </div>
          </div>
          <div class="box-body">
            <div
              class="flex gap-5 items-center p-4 justify-around bg-light mx-2 flex-wrap flex-xl-nowrap rounded-md"
            >
              <div class="flex gap-4 items-center flex-wrap">
                <div
                  class="avatar avatar-lg flex-shrink-0 bg-primary/10 avatar-rounded svg-primary shadow-sm border border-primary border-opacity-25"
                >
                  <i class="ri-stack-line text-2xl text-primary"></i>
                </div>
                <div>
                  <span class="mb-1 block">Total Revenue</span>
                  <div class="flex align-items-end gap-2">
                    <h4 class="mb-0">$475,896</h4>
                    <div class="text-[13px]">
                      <span class="opacity-70"> Increased By </span>
                      <span class="badge leading-none bg-success align-middle opacity-90">
                        5.6%<i class="ti ti-trending-up"></i>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="flex gap-4 items-center flex-wrap">
                <div
                  class="avatar avatar-lg flex-shrink-0 bg-primarytint1color/10 avatar-rounded svg-primarytint1color shadow-sm border border-primarytint1color border-opacity-25"
                >
                  <i class="ri-briefcase-line text-2xl text-primarytint1color"></i>
                </div>
                <div>
                  <span class="mb-1 block">Total Projects</span>
                  <div class="flex align-items-end gap-2">
                    <h4 class="mb-0">75,896</h4>
                    <div class="text-[13px]">
                      <span class="opacity-70"> Increased By </span>
                      <span class="badge leading-none bg-danger align-middle opacity-90">
                        1.6%<i class="ti ti-trending-down"></i>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Project Statistics Chart -->
            <div id="project-statistics" class="mt-4"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Row 2: Running Projects, Monthly Targets & Daily Tasks -->
    <div class="grid grid-cols-12 gap-x-6 mt-6">
      <!-- Running Projects List -->
      <div class="xxl:col-span-4 col-span-12">
        <div class="box">
          <div class="box-header justify-between">
            <div class="box-title">Running Projects List</div>
            <button class="ti-btn ti-btn-sm bg-primary/10 text-primary" type="button">View All</button>
          </div>
          <div class="p-4 space-y-4">
            <!-- Project 1 -->
            <div>
              <div class="flex items-start gap-4 mb-3">
                <div class="grow">
                  <div class="flex items-center gap-1">
                    <p class="font-medium mb-1 text-[14px]">Web application design</p>
                    <a class="text-info hs-tooltip ti-main-tooltip" href="javascript:void(0);">
                      <i class="ri-information-2-line text-[13px] opacity-70 leading-none align-middle mb-1"></i>
                      <span
                        class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm"
                        role="tooltip"
                      >
                        Get Info
                      </span>
                    </a>
                  </div>
                  <p class="text-textmuted dark:text-textmuted/50 mb-1 font-normal text-xs">
                    At vero eos et accusamus et iusto odio.
                  </p>
                  <div>
                    Status: <span class="text-success font-normal text-xs">75% completed</span>
                  </div>
                </div>
                <div class="flex-shrink-0 text-end ms-auto">
                  <p class="mb-3 text-[11px] text-textmuted dark:text-textmuted/50">
                    <i class="ri-time-line text-textmuted dark:text-textmuted/50 text-[11px] align-middle leading-none me-1 inline-block"></i>2mins ago
                  </p>
                  <div class="avatar-list-stacked">
                    <span class="avatar avatar-sm avatar-rounded">
                      <img alt="img" src="@/assets/images/11.jpg" />
                    </span>
                    <span class="avatar avatar-sm avatar-rounded">
                      <img alt="img" src="@/assets/images/2.jpg" />
                    </span>
                    <span class="avatar avatar-sm avatar-rounded">
                      <img alt="img" src="@/assets/images/5.jpg" />
                    </span>
                    <span class="avatar avatar-sm avatar-rounded">
                      <img alt="img" src="@/assets/images/6.jpg" />
                    </span>
                  </div>
                </div>
              </div>
              <div>
                <div
                  aria-valuemax="100"
                  aria-valuemin="0"
                  aria-valuenow="90"
                  class="progress progress-lg !rounded-full p-1 ms-auto bg-primary/10"
                  role="progressbar"
                >
                  <div class="progress-bar progress-bar-striped progress-bar-animated !rounded-full" style="width: 90%"></div>
                </div>
              </div>
            </div>

            <!-- Project 2 -->
            <div>
              <div class="flex items-start gap-4 mb-3">
                <div class="grow">
                  <div class="flex items-center gap-1">
                    <p class="font-medium mb-1 text-[14px]">Designing New Template</p>
                    <a class="text-info hs-tooltip ti-main-tooltip" href="javascript:void(0);">
                      <i class="ri-information-2-line text-[13px] opacity-70 leading-none align-middle mb-1"></i>
                      <span
                        class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm"
                        role="tooltip"
                      >
                        Get Info
                      </span>
                    </a>
                  </div>
                  <p class="text-textmuted dark:text-textmuted/50 mb-1 font-normal text-xs">
                    At vero eos et accusamus et iusto odio.
                  </p>
                  <div>
                    Status: <span class="text-warning font-medium text-xs">45% completed</span>
                  </div>
                </div>
                <div class="flex-shrink-0 text-end ms-auto">
                  <p class="mb-3 text-[11px] text-textmuted dark:text-textmuted/50">
                    <i class="ri-time-line text-textmuted dark:text-textmuted/50 text-[11px] align-middle leading-none me-1 inline-block"></i>15mins ago
                  </p>
                  <div class="avatar-list-stacked">
                    <span class="avatar avatar-sm avatar-rounded">
                      <img alt="img" src="@/assets/images/11.jpg" />
                    </span>
                    <span class="avatar avatar-sm avatar-rounded">
                      <img alt="img" src="@/assets/images/8.jpg" />
                    </span>
                    <span class="avatar avatar-sm avatar-rounded">
                      <img alt="img" src="@/assets/images/2.jpg" />
                    </span>
                  </div>
                </div>
              </div>
              <div>
                <div
                  aria-valuemax="100"
                  aria-valuemin="0"
                  aria-valuenow="45"
                  class="progress progress-lg !rounded-full p-1 ms-auto flex-auto bg-primarytint1color/10"
                  role="progressbar"
                >
                  <div
                    class="progress-bar bg-primarytint1color progress-bar-striped progress-bar-animated !rounded-full"
                    style="width: 45%"
                  ></div>
                </div>
              </div>
            </div>

            <!-- Project 3 -->
            <div>
              <div class="flex items-start gap-4 mb-3">
                <div class="grow">
                  <div class="flex items-center gap-1">
                    <p class="font-medium mb-1 text-[14px]">Projects Work Progress</p>
                    <a class="text-info hs-tooltip ti-main-tooltip" href="javascript:void(0);">
                      <i class="ri-information-2-line text-[13px] opacity-70 leading-none align-middle mb-1"></i>
                      <span
                        class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm"
                        role="tooltip"
                      >
                        Get Info
                      </span>
                    </a>
                  </div>
                  <p class="text-textmuted dark:text-textmuted/50 mb-1 font-normal text-xs">
                    At vero eos et accusamus et iusto odio.
                  </p>
                  <div>
                    Status: <span class="text-success font-medium text-xs">65% completed</span>
                  </div>
                </div>
                <div class="flex-shrink-0 text-end ms-auto">
                  <p class="mb-3 text-[11px] text-textmuted dark:text-textmuted/50">
                    <i class="ri-time-line text-textmuted dark:text-textmuted/50 text-[11px] align-middle leading-none me-1 inline-block"></i>20mins ago
                  </p>
                  <div class="avatar-list-stacked">
                    <span class="avatar avatar-sm avatar-rounded">
                      <img alt="img" src="@/assets/images/15.jpg" />
                    </span>
                    <span class="avatar avatar-sm avatar-rounded">
                      <img alt="img" src="@/assets/images/3.jpg" />
                    </span>
                    <a class="avatar avatar-sm bg-primary border-2 avatar-rounded text-white" href="javascript:void(0);">
                      2+
                    </a>
                  </div>
                </div>
              </div>
              <div>
                <div
                  aria-valuemax="100"
                  aria-valuemin="0"
                  aria-valuenow="65"
                  class="progress progress-lg !rounded-full p-1 ms-auto flex-auto bg-primarytint2color/10"
                  role="progressbar"
                >
                  <div
                    class="progress-bar bg-primarytint2color progress-bar-striped progress-bar-animated !rounded-full"
                    style="width: 65%"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Monthly Targets & Daily Tasks -->
      <div class="xxl:col-span-8 col-span-12">
        <div class="grid grid-cols-12 gap-x-6">
          <!-- Monthly Targets -->
          <div class="xxl:col-span-4 lg:col-span-6 col-span-12">
            <div class="box">
              <div class="box-header justify-between">
                <div class="box-title">Monthly Targets</div>
                <a class="ti-btn ti-btn-sm bg-light" href="javascript:void(0);">View All</a>
              </div>
              <div class="box-body">
                <div id="monthly-target" class="mb-4"></div>
                <div class="flex gap-4 items-center justify-between text-center p-4 bg-light rounded-md">
                  <div>
                    <span class="mb-1 block">
                      <i class="ri-circle-fill text-[8px] text-primary align-middle"></i>
                      New Projects
                    </span>
                    <h6 class="mb-1">4,896</h6>
                    <span class="text-success font-medium">
                      <i class="ri-arrow-up-s-fill"></i> 3.5%
                    </span>
                  </div>
                  <div>
                    <span class="mb-1 block">
                      <i class="ri-circle-fill text-[8px] text-primarytint1color align-middle"></i>
                      Completed
                    </span>
                    <h6 class="mb-1">2,475</h6>
                    <span class="text-danger font-medium">
                      <i class="ri-arrow-down-s-fill"></i> 1.5%
                    </span>
                  </div>
                  <div>
                    <span class="mb-1 block">
                      <i class="ri-circle-fill text-[8px] text-primarytint2color align-middle"></i>
                      Pending
                    </span>
                    <h6 class="mb-1">456</h6>
                    <span class="text-success font-medium">
                      <i class="ri-arrow-up-s-fill"></i> 0.1%
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Daily Tasks -->
          <div class="xxl:col-span-8 lg:col-span-6 col-span-12">
            <div class="box">
              <div class="box-header justify-between">
                <div class="box-title">Daily Tasks</div>
                <div class="ti-dropdown hs-dropdown">
                  <a
                    aria-expanded="false"
                    class="ti-btn ti-btn-sm bg-light"
                    data-bs-toggle="dropdown"
                    href="javascript:void(0);"
                  >
                    View All<i class="ri-arrow-down-s-line align-middle ms-1 inline-block"></i>
                  </a>
                  <ul class="ti-dropdown-menu hs-dropdown-menu hidden" role="menu">
                    <li><a class="ti-dropdown-item" href="javascript:void(0);">Download</a></li>
                    <li><a class="ti-dropdown-item" href="javascript:void(0);">Import</a></li>
                    <li><a class="ti-dropdown-item" href="javascript:void(0);">Export</a></li>
                  </ul>
                </div>
              </div>
              <div class="box-body">
                <ul class="ti-list-group ti-list-group-flush list-none">
                  <!-- Task 1 -->
                  <li class="ti-list-group-item !border-b-0 flex gap-4 !p-0 items-start mb-2">
                    <div class="flex-shrink-0 daily-tasks-time">
                      <span class="text-textmuted dark:text-textmuted/50 ms-auto text-[11px] flex-shrink-0 flex-auto"
                        >09:15 AM</span
                      >
                    </div>
                    <div class="box border border-primary/25 shadow-none mb-0 bg-primary/10 w-full">
                      <div class="box-body">
                        <div class="flex items-center gap-2 justify-between">
                          <p class="font-medium mb-2 leading-none">Home Page Design</p>
                          <div class="hs-tooltip ti-main-tooltip">
                            <a aria-label="anchor" class="float-end text-[1rem] text-primary" href="javascript:void(0);">
                              <i class="ri-add-circle-fill"></i>
                              <span
                                class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm"
                                role="tooltip"
                              >
                                View Details
                              </span>
                            </a>
                          </div>
                        </div>
                        <div class="flex flex-wrap gap-2 items-center">
                          <span class="badge leading-none bg-primary/10 text-primary">Framework</span>
                          <span class="badge leading-none bg-secondary/10 text-secondary">Angular</span>
                          <span class="badge leading-none bg-info/10 text-info">Php</span>
                          <div class="avatar-list-stacked ms-auto">
                            <span class="avatar avatar-xs avatar-rounded">
                              <img alt="img" src="@/assets/images/2.jpg" />
                            </span>
                            <span class="avatar avatar-xs avatar-rounded">
                              <img alt="img" src="@/assets/images/12.jpg" />
                            </span>
                            <span class="avatar avatar-xs avatar-rounded">
                              <img alt="img" src="@/assets/images/8.jpg" />
                            </span>
                            <span class="avatar avatar-xs avatar-rounded">
                              <img alt="img" src="@/assets/images/2.jpg" />
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>

                  <!-- Task 2 -->
                  <li class="ti-list-group-item !border-b-0 flex gap-4 !p-0 items-start pt-1 mb-2">
                    <div class="flex-shrink-0 daily-tasks-time">
                      <span class="text-textmuted dark:text-textmuted/50 ms-auto text-[11px] flex-shrink-0 flex-auto"
                        >10:15 AM</span
                      >
                    </div>
                    <div
                      class="box border border-primarytint1color/25 shadow-none mb-0 bg-primarytint1color/10 w-full"
                    >
                      <div class="box-body">
                        <div class="flex items-center gap-2 justify-between">
                          <p class="font-medium mb-2 leading-none">Meeting Hour</p>
                          <div class="hs-tooltip ti-main-tooltip">
                            <a
                              aria-label="anchor"
                              class="float-end text-[1rem] text-primarytint1color"
                              href="javascript:void(0);"
                            >
                              <i class="ri-add-circle-fill"></i>
                              <span
                                class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm"
                                role="tooltip"
                              >
                                View Details
                              </span>
                            </a>
                          </div>
                        </div>
                        <div class="flex flex-wrap gap-2 items-center">
                          <span class="badge leading-none bg-primary/10 text-primary">Framework</span>
                          <span class="badge leading-none bg-secondary/10 text-secondary">Angular</span>
                          <span class="badge leading-none bg-info/10 text-info">Php</span>
                          <span class="badge leading-none bg-danger/10 text-danger">Html</span>
                          <span class="badge leading-none bg-success/10 text-success">Laravel</span>
                          <div class="avatar-list-stacked ms-auto">
                            <span class="avatar avatar-xs avatar-rounded">
                              <img alt="img" src="@/assets/images/2.jpg" />
                            </span>
                            <span class="avatar avatar-xs avatar-rounded">
                              <img alt="img" src="@/assets/images/12.jpg" />
                            </span>
                            <span class="avatar avatar-xs avatar-rounded">
                              <img alt="img" src="@/assets/images/8.jpg" />
                            </span>
                            <span class="avatar avatar-xs avatar-rounded">
                              <img alt="img" src="@/assets/images/2.jpg" />
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>

                  <!-- Task 3 -->
                  <li class="ti-list-group-item !border-b-0 flex gap-4 !p-0 items-start pt-1 mb-2">
                    <div class="flex-shrink-0 daily-tasks-time">
                      <span class="text-textmuted dark:text-textmuted/50 ms-auto text-[11px] flex-shrink-0 flex-auto"
                        >04:30 AM</span
                      >
                    </div>
                    <div
                      class="box border border-primarytint2color/25 shadow-none mb-0 bg-primarytint2color/10 w-full"
                    >
                      <div class="box-body">
                        <div class="flex items-center gap-2 justify-between">
                          <p class="font-medium mb-2 leading-none">Projects Work Progress</p>
                          <div class="hs-tooltip ti-main-tooltip">
                            <a
                              aria-label="anchor"
                              class="float-end text-[1rem] text-primarytint2color"
                              href="javascript:void(0);"
                            >
                              <i class="ri-add-circle-fill"></i>
                              <span
                                class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm"
                                role="tooltip"
                              >
                                View Details
                              </span>
                            </a>
                          </div>
                        </div>
                        <div class="flex flex-wrap gap-2 items-center">
                          <span class="badge leading-none bg-info/10 text-info">Php</span>
                          <span class="badge leading-none bg-danger/10 text-danger">Html</span>
                          <span class="badge leading-none bg-primary/10 text-primary">Framework</span>
                          <div class="avatar-list-stacked ms-auto">
                            <span class="avatar avatar-xs avatar-rounded">
                              <img alt="img" src="@/assets/images/2.jpg" />
                            </span>
                            <span class="avatar avatar-xs avatar-rounded">
                              <img alt="img" src="@/assets/images/12.jpg" />
                            </span>
                            <span class="avatar avatar-xs avatar-rounded">
                              <img alt="img" src="@/assets/images/8.jpg" />
                            </span>
                            <span class="avatar avatar-xs avatar-rounded">
                              <img alt="img" src="@/assets/images/2.jpg" />
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Row 3: Projects Summary & Task Summary -->
    <div class="grid grid-cols-12 gap-x-6 mt-6">
      <!-- Projects Summary table -->
      <div class="xxl:col-span-9 col-span-12">
        <div class="box">
          <div class="box-header justify-between">
            <div class="box-title">Projects Summary</div>
            <div class="flex flex-wrap">
              <div class="me-3 my-1">
                <input
                  aria-label=".form-control-sm example"
                  class="ti-form-control form-control-sm"
                  placeholder="Search Here"
                  type="text"
                />
              </div>
              <div class="ti-dropdown hs-dropdown my-1">
                <a
                  aria-expanded="false"
                  class="ti-btn bg-primary !m-0 text-white ti-btn-sm ti-dropdown-toggle hs-dropdown-toggle"
                  data-bs-toggle="dropdown"
                  href="javascript:void(0);"
                >
                  Sort By<i class="ri-arrow-down-s-line align-middle ms-1 inline-block"></i>
                </a>
                <ul class="ti-dropdown-menu hs-dropdown-menu hidden" role="menu">
                  <li><a class="ti-dropdown-item" href="javascript:void(0);">New</a></li>
                  <li><a class="ti-dropdown-item" href="javascript:void(0);">Popular</a></li>
                  <li><a class="ti-dropdown-item" href="javascript:void(0);">Relevant</a></li>
                </ul>
              </div>
            </div>
          </div>
          <div class="box-body">
            <div class="table-responsive overflow-auto table-bordered-default">
              <table class="table table-hover whitespace-nowrap">
                <thead>
                  <tr class="border-b border-defaultborder dark:border-defaultborder/10">
                    <th scope="col">S.No</th>
                    <th scope="col">Poject Title</th>
                    <th scope="col">Tasks</th>
                    <th scope="col">Progress</th>
                    <th scope="col">Assigned Team</th>
                    <th scope="col">Status</th>
                    <th scope="col">Due Date</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>1</td>
                    <td>
                      <span class="font-medium"> Home Page</span>
                    </td>
                    <td>210 <span class="opacity-70">/234</span></td>
                    <td>
                      <div class="flex items-center">
                        <div
                          aria-valuemax="100"
                          aria-valuemin="0"
                          aria-valuenow="35"
                          class="progress progress-sm w-full"
                          role="progressbar"
                        >
                          <div class="progress-bar bg-primary" style="width: 35%"></div>
                        </div>
                        <div class="ms-2">35%</div>
                      </div>
                    </td>
                    <td>
                      <div class="avatar-list-stacked">
                        <span class="avatar avatar-xs avatar-rounded">
                          <img alt="img" src="@/assets/images/8.jpg" />
                        </span>
                        <span class="avatar avatar-xs avatar-rounded">
                          <img alt="img" src="@/assets/images/4.jpg" />
                        </span>
                        <span class="avatar avatar-xs avatar-rounded">
                          <img alt="img" src="@/assets/images/6.jpg" />
                        </span>
                        <span class="avatar avatar-xs avatar-rounded">
                          <img alt="img" src="@/assets/images/7.jpg" />
                        </span>
                      </div>
                    </td>
                    <td>
                      <span class="badge leading-none bg-primary/10 text-primary">In Progress</span>
                    </td>
                    <td>14-05-2024</td>
                    <td>
                      <div class="flex items-center gap-2">
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-2 ti-btn-soft-primary !m-0"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-eye"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              View
                            </span>
                          </a>
                        </div>
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-2 ti-btn-soft-secondary !m-0"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-pencil"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              Edit
                            </span>
                          </a>
                        </div>
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-2 ti-btn-soft-danger !m-0"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-trash"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              Delete
                            </span>
                          </a>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>2</td>
                    <td>
                      <span class="font-medium"> Landing Design </span>
                    </td>
                    <td>162 <span class="op-8">/185</span></td>
                    <td>
                      <div class="flex items-center">
                        <div
                          aria-valuemax="100"
                          aria-valuemin="0"
                          aria-valuenow="80"
                          class="progress progress-sm w-full"
                          role="progressbar"
                        >
                          <div class="progress-bar bg-primary" style="width: 80%"></div>
                        </div>
                        <div class="ms-2">80%</div>
                      </div>
                    </td>
                    <td>
                      <div class="avatar-list-stacked">
                        <span class="avatar avatar-xs avatar-rounded">
                          <img alt="img" src="@/assets/images/8.jpg" />
                        </span>
                        <span class="avatar avatar-xs avatar-rounded">
                          <img alt="img" src="@/assets/images/4.jpg" />
                        </span>
                        <span class="avatar avatar-xs avatar-rounded">
                          <img alt="img" src="@/assets/images/6.jpg" />
                        </span>
                        <span class="avatar avatar-xs avatar-rounded">
                          <img alt="img" src="@/assets/images/7.jpg" />
                        </span>
                      </div>
                    </td>
                    <td>
                      <span class="badge leading-none bg-primary/10 text-primary">In Progress</span>
                    </td>
                    <td>20-05-2024</td>
                    <td>
                      <div class="flex items-center gap-2">
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-2 ti-btn-soft-primary !m-0"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-eye"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              View
                            </span>
                          </a>
                        </div>
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-2 ti-btn-soft-secondary !m-0"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-pencil"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              Edit
                            </span>
                          </a>
                        </div>
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-2 ti-btn-soft-danger !m-0"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-trash"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              Delete
                            </span>
                          </a>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>3</td>
                    <td>
                      <span class="font-medium"> Mobile App Development </span>
                    </td>
                    <td>145 <span class="opacity-70">/180</span></td>
                    <td>
                      <div class="flex items-center">
                        <div
                          aria-valuemax="100"
                          aria-valuemin="0"
                          aria-valuenow="65"
                          class="progress progress-sm w-full"
                          role="progressbar"
                        >
                          <div class="progress-bar bg-primary" style="width: 65%"></div>
                        </div>
                        <div class="ms-2">65%</div>
                      </div>
                    </td>
                    <td>
                      <div class="avatar-list-stacked">
                        <span class="avatar avatar-xs avatar-rounded">
                          <img alt="img" src="@/assets/images/8.jpg" />
                        </span>
                        <span class="avatar avatar-xs avatar-rounded">
                          <img alt="img" src="@/assets/images/4.jpg" />
                        </span>
                        <span class="avatar avatar-xs avatar-rounded">
                          <img alt="img" src="@/assets/images/6.jpg" />
                        </span>
                        <span class="avatar avatar-xs avatar-rounded">
                          <img alt="img" src="@/assets/images/7.jpg" />
                        </span>
                      </div>
                    </td>
                    <td>
                      <span class="badge leading-none bg-success/10 text-success">Completed</span>
                    </td>
                    <td>25-05-2024</td>
                    <td>
                      <div class="flex items-center gap-2">
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-2 ti-btn-soft-primary !m-0"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-eye"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              View
                            </span>
                          </a>
                        </div>
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-2 ti-btn-soft-secondary !m-0"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-pencil"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              Edit
                            </span>
                          </a>
                        </div>
                        <div class="hs-tooltip ti-main-tooltip [--placement:top]">
                          <a
                            aria-label="anchor"
                            class="hs-tooltip-toggle ti-btn ti-btn-icon ti-btn-sm !rounded-full me-2 ti-btn-soft-danger !m-0"
                            href="javascript:void(0);"
                          >
                            <i class="ti ti-trash"></i>
                            <span
                              class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 !bg-black !text-xs !font-medium !text-white shadow-sm dark:bg-slate-700"
                              role="tooltip"
                            >
                              Delete
                            </span>
                          </a>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Task Summary -->
      <div class="xxl:col-span-3 col-span-12">
        <div class="box overflow-hidden">
          <div class="box-header justify-between">
            <div class="box-title">Task Summary</div>
            <a class="ti-btn ti-btn-sm bg-light" href="javascript:void(0);">View All</a>
          </div>
          <div class="box-body">
            <div class="flex gap-4 items-center justify-between p-4 bg-light mb-4 rounded-md">
              <div>
                <h6 class="mb-1">Tasks Completed Rate</h6>
                <p class="mb-0 text-textmuted dark:text-textmuted/50">Within the Deadline</p>
              </div>
              <div>
                <h5 class="mb-0">
                  85%
                  <span class="badge leading-none bg-success text-white font-medium text-[8px] ms-2">
                    <i class="ri-arrow-up-s-fill"></i> 1.5%
                  </span>
                </h5>
              </div>
            </div>
            <div id="tasks-report"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

