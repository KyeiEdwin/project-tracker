# Agile Module - User Manual

## Table of Contents

1. [Introduction](#introduction)
2. [Getting Started](#getting-started)
3. [Product Backlog Management](#product-backlog-management)
4. [Sprint Management](#sprint-management)
5. [Metrics and Analytics](#metrics-and-analytics)
6. [Best Practices](#best-practices)
7. [Troubleshooting](#troubleshooting)
8. [Glossary](#glossary)

---

## Introduction

### What is the Agile Module?

The Agile Module is a comprehensive sprint planning and backlog management system that helps teams implement Scrum and Agile methodologies effectively. It provides:

- **Hierarchical Backlog Management** - Organize work from epics down to tasks
- **Sprint Planning** - Plan and execute time-boxed iterations
- **Automatic Calculations** - No manual entry of story points
- **Velocity Tracking** - Monitor team performance over time
- **Burndown Charts** - Visualize sprint progress in real-time
- **Complete Audit Trail** - Track all changes and decisions

### Who Should Use This?

- **Product Owners** - Manage and prioritize the product backlog
- **Scrum Masters** - Facilitate sprints and track team velocity
- **Development Teams** - View and update work items during sprints
- **Stakeholders** - Monitor project progress and sprint outcomes

---

## Getting Started

### Accessing the Agile Module

1. Log in to the Project Tracker
2. Click on **Agile** in the main navigation
3. Choose between:
   - **Sprints** - Manage sprint cycles
   - **Backlog** - Manage product backlog
   - **Definitions** - Configure agile settings

### Initial Setup

Before starting, ensure you have:

1. ✅ Created at least one project
2. ✅ Set up team members with appropriate permissions
3. ✅ Defined your backlog structure (Epics, Features, Stories)

---

## Product Backlog Management

### Understanding Backlog Hierarchy

The system supports a multi-level hierarchy:

```
Epic (Large initiative, 1-3 months)
  ├─ Feature (Significant functionality, 2-4 weeks)
  │   ├─ Story (User-facing value, 1-5 days)
  │   │   ├─ Task (Implementation detail, hours-1 day)
  │   │   └─ Bug (Fix for defect)
  │   └─ Story
  └─ Feature
```

### Creating Backlog Items

#### Method 1: Quick Add (Recommended for most items)

1. Go to **Agile** → **Backlog**
2. Click **+ Add Backlog Item**
3. Fill in the quick form:
   - **Title**: Clear, concise description
   - **Type**: Epic, Feature, Story, Task, or Bug
   - **Story Points**: Estimate using Fibonacci sequence (0, 1, 2, 3, 5, 8, 13, 21)
   - **Priority**: Low, Medium, High, or Critical
4. Click **Create Item**

#### Method 2: Add Child Item (For hierarchies)

1. In the hierarchy tree view, hover over a parent item
2. Click the **➕** (plus) icon
3. The quick add form will pre-fill the parent relationship
4. Only valid child types will be shown (e.g., Stories under Features)

### Organizing Your Backlog

#### View Modes

**Hierarchy View** 🌳
- See parent-child relationships
- Expand/collapse sections
- Drag-and-drop to reorganize
- Best for: Planning and grooming sessions

**List View** 📋
- Kanban-style columns by status
- Quick status overview
- Best for: Status updates and daily standups

#### Drag-and-Drop

1. Switch to **Hierarchy View**
2. Click and drag an item
3. Drop it onto a new parent
4. The system validates the relationship automatically

### Estimating Work

#### Story Points Guidelines

| Points | Complexity | Duration | Example |
|--------|-----------|----------|---------|
| 1 | Trivial | 1-2 hours | Fix typo, update text |
| 2 | Simple | Half day | Add validation rule |
| 3 | Easy | 1 day | Create simple form |
| 5 | Medium | 2-3 days | Implement API endpoint |
| 8 | Complex | 1 week | Build feature with UI |
| 13 | Very Complex | 2 weeks | Complex integration |
| 21 | Epic-sized | 3+ weeks | Should be split! |

💡 **Pro Tip**: If an item is 13+ points, consider splitting it into smaller stories.

#### Priority Levels

- **Critical** 🔴 - Blocking issue, production down
- **High** 🟠 - Important feature, needed for release
- **Medium** 🟡 - Normal work, plan accordingly
- **Low** 🔵 - Nice-to-have, do when time permits

### Backlog Refinement

Regular backlog grooming keeps your backlog healthy:

1. **Weekly Grooming Sessions** (1-2 hours)
   - Review new items
   - Update estimates
   - Re-prioritize based on value
   - Split large items

2. **Acceptance Criteria**
   - Add detailed descriptions
   - Define "done" conditions
   - Include edge cases

3. **Keep It Clean**
   - Archive completed items
   - Remove outdated stories
   - Merge duplicates

---

## Sprint Management

### Understanding Sprints

A sprint is a time-boxed iteration (typically 1-4 weeks) where the team commits to completing a set of backlog items.

#### Sprint Lifecycle

```
📝 Planned → ▶️ Active → ✅ Completed (Immutable)
```

### Creating a Sprint

1. Go to **Agile** → **Sprints**
2. Click **+ Create Sprint**
3. Fill in sprint details:
   - **Name**: Sprint 1, Sprint 2, or use creative names
   - **Goal**: What you want to achieve (1-2 sentences)
   - **Start Date**: Sprint beginning (usually Monday)
   - **End Date**: Sprint conclusion (usually Friday)
   - **Status**: Select "Planned"
4. Click **Create**

### Sprint Planning

#### Adding Items to Sprint

**Method 1: From Backlog**
1. Go to backlog
2. Select items for the sprint
3. Use **Add to Sprint** action
4. Choose the target sprint

**Method 2: From Sprint View**
1. Open the sprint
2. Click **Add Items**
3. Select from available backlog items
4. Click **Add to Sprint**

#### Planning Meeting Workflow

1. **Review Sprint Goal** (5 min)
   - What are we trying to achieve?
   - How does this align with product goals?

2. **Review Team Capacity** (5 min)
   - Who's available?
   - Any holidays/vacations?
   - Expected velocity based on history

3. **Select Items** (45 min)
   - Start with highest priority
   - Discuss each item
   - Team commits to items
   - Stop when capacity is reached

4. **Confirm and Start** (5 min)
   - Review selected items
   - Verify everyone understands
   - Start the sprint!

### Starting a Sprint

1. Ensure sprint has backlog items assigned
2. Verify start and end dates are correct
3. Click **Start Sprint** button
4. Confirm the action

**What Happens:**
- ✅ Sprint status changes to "Active"
- ✅ First daily snapshot is created
- ✅ Burndown chart becomes available
- ✅ Sprint event is logged
- ✅ Any expired active sprints are auto-closed

**Restrictions:**
- ⚠️ Only one active sprint per project
- ⚠️ Cannot start if another sprint is active
- ⚠️ Cannot start a completed sprint

### During the Sprint

#### Daily Standup

Use the sprint board to facilitate daily standups:

1. Open the active sprint
2. Review the metrics card:
   - **Planned Points**: Total commitment
   - **Completed Points**: Work done
   - **Remaining Points**: Work left
   - **Completion Rate**: Progress percentage

3. Check the burndown chart:
   - Are we ahead or behind schedule?
   - Is the actual line tracking the ideal line?

4. Update work status:
   - Mark items as "in-progress"
   - Mark completed items as "done"
   - Points update automatically!

#### Burndown Chart

The burndown chart shows:

- **Ideal Line** (gray, dashed): Perfect linear progress
- **Actual Line** (blue, solid): Real remaining work
- **Date Range**: Sprint start to end
- **Status Message**: Ahead or behind schedule

**Reading the Chart:**
- ✅ **Actual below ideal**: Ahead of schedule! 🎉
- ⚠️ **Actual above ideal**: Behind schedule, adjust!
- 📈 **Flat actual line**: No progress made (review impediments)
- 📊 **Actual line goes up**: Scope increased (discuss with team)

#### Mid-Sprint Adjustments

**Adding Items** ⚠️
- Avoid adding items mid-sprint if possible
- If urgent work appears:
  1. Discuss with Product Owner
  2. Consider removing lower priority items
  3. Document scope change
  4. Update sprint goal if needed

**Removing Items**
- If an item can't be completed:
  1. Discuss with team
  2. Remove from sprint
  3. Return to backlog
  4. Document reason

**Changing Estimates**
- If estimate was wrong:
  1. Update the story points
  2. Sprint totals recalculate automatically
  3. Burndown adjusts accordingly
  4. Discuss in retrospective

### Closing a Sprint

At the end of the sprint:

1. Ensure all items are updated
2. Click **Close Sprint** button
3. Confirm the action

**What Happens:**
- ✅ Sprint status changes to "Completed"
- ✅ Final snapshot is created
- ✅ Sprint becomes **immutable** (cannot modify)
- ✅ Velocity is calculated
- ✅ Sprint event is logged

**Sprint Review Meeting:**

1. **Demo Completed Work** (30 min)
   - Show working software
   - Get stakeholder feedback

2. **Review Metrics** (10 min)
   - Planned vs completed points
   - Completion rate
   - Items completed vs planned

3. **Update Product Backlog** (20 min)
   - Reprioritize based on feedback
   - Add new items discovered
   - Archive completed items

**Sprint Retrospective:**

1. **What Went Well?** ✅
   - Celebrate successes
   - Identify practices to continue

2. **What Didn't Go Well?** ⚠️
   - Discuss challenges
   - No blame, focus on improvement

3. **Action Items** 📝
   - Concrete improvements
   - Assign owners
   - Track in next sprint

---

## Metrics and Analytics

### Velocity

**What is Velocity?**
Velocity is the average number of story points your team completes per sprint. It helps predict future capacity.

**Viewing Velocity:**
1. Go to **Agile** → **Sprints**
2. Velocity is displayed on the sprint overview
3. Shows:
   - Average velocity (last 3 sprints by default)
   - Sprint-by-sprint breakdown
   - Trend (improving/declining/stable)

**Using Velocity:**

```
Next Sprint Capacity = Average Velocity
```

Example:
- Sprint 1: 25 points completed
- Sprint 2: 30 points completed  
- Sprint 3: 28 points completed
- **Average Velocity**: 27.7 points

For Sprint 4, plan for approximately 27-28 points.

**Tips:**
- ✅ Use average of last 3-5 sprints
- ✅ Account for team changes (vacations, new members)
- ✅ Velocity should stabilize over time
- ⚠️ Don't compare velocity between teams!
- ⚠️ Don't use velocity as performance metric

### Completion Rate

Shows what percentage of planned work was completed.

**Healthy Targets:**
- 🎯 **80-100%**: Excellent planning and execution
- ⚠️ **60-79%**: Room for improvement
- ❌ **<60%**: Over-committed or impediments

**If Completion Rate is Low:**
1. Review sprint planning process
2. Check for external interruptions
3. Verify estimates are accurate
4. Discuss team capacity

### Historical Data

All sprint data is preserved:

- **Sprint Snapshots**: Daily progress records
- **Sprint Events**: Complete audit trail
- **Metrics**: Velocity, completion rates, trends

**Accessing History:**
1. Go to completed sprint
2. View burndown chart (historical)
3. Check event log for timeline
4. Review metrics for insights

---

## Best Practices

### For Product Owners

1. **Keep Backlog Groomed**
   - At least 2 sprints worth of ready items
   - Top items well-defined with acceptance criteria
   - Bottom items can be rough ideas

2. **Prioritize Ruthlessly**
   - Focus on value delivery
   - Say no to low-value work
   - Re-evaluate priorities regularly

3. **Engage with Team**
   - Be available for questions
   - Attend sprint events
   - Provide timely feedback

### For Scrum Masters

1. **Facilitate, Don't Dictate**
   - Let team self-organize
   - Remove impediments
   - Coach, don't command

2. **Track Metrics**
   - Monitor velocity trends
   - Watch burndown patterns
   - Identify improvement areas

3. **Continuous Improvement**
   - Act on retrospective items
   - Experiment with processes
   - Measure impact of changes

### For Development Teams

1. **Update Status Daily**
   - Mark items in progress
   - Complete items promptly
   - Keep burndown accurate

2. **Communicate Early**
   - Raise blockers immediately
   - Ask for help when stuck
   - Share knowledge freely

3. **Respect Sprint Commitment**
   - Focus on sprint goal
   - Avoid scope creep
   - Deliver working software

### General Tips

✅ **DO:**
- Use consistent sprint lengths (2 weeks recommended)
- Maintain sustainable pace
- Focus on done items over in-progress
- Celebrate successes
- Learn from failures

❌ **DON'T:**
- Change sprint scope frequently
- Skip sprint events
- Work on non-sprint items
- Compare velocity between teams
- Use points for time tracking

---

## Troubleshooting

### Cannot Start Sprint

**Error**: "Another sprint is already active for this project"

**Solution:**
1. Check for active sprints in the same project
2. Close the active sprint first
3. Or let it expire and try again (auto-closes expired sprints)

---

**Error**: "Sprint has no backlog items assigned"

**Solution:**
1. Add at least one backlog item to the sprint
2. Assign story points to items
3. Try starting again

---

### Cannot Modify Sprint

**Error**: "Cannot modify completed sprint"

**Solution:**
- Completed sprints are **immutable** by design
- This preserves historical accuracy
- Create a new sprint for new work
- If data is truly incorrect, contact administrator

---

### Points Not Updating

**Issue**: Sprint points don't reflect backlog changes

**Solution:**
1. Wait a moment (updates happen automatically)
2. Refresh the page
3. Check if items are actually assigned to the sprint
4. Verify items have story points assigned

---

### Burndown Chart Not Showing

**Issue**: No burndown chart visible

**Possible Causes:**
1. **Sprint not started** - Chart only appears for active sprints
2. **No snapshots yet** - Wait until after midnight for first snapshot
3. **Browser issue** - Hard refresh (Ctrl+F5)

---

### Circular Reference Error

**Error**: "Circular parent reference detected"

**Solution:**
- You're trying to create a loop (A parent of B, B parent of A)
- Choose a different parent
- Check the hierarchy structure

---

### Cross-Project Error

**Error**: "Parent must belong to the same project"

**Solution:**
- Parent and child must be in the same project
- Verify you're creating the item in the correct project
- Check the parent item's project

---

## Glossary

### Agile Terms

**Backlog**: Prioritized list of work items for a product

**Epic**: Large body of work that spans multiple sprints (20+ points)

**Feature**: Significant product functionality (8-13 points)

**Story**: User-facing value delivered in one sprint (1-8 points)

**Task**: Technical work item, part of a story (hours to 1 day)

**Bug**: Defect that needs fixing

**Sprint**: Time-boxed iteration (1-4 weeks)

**Story Points**: Relative estimate of effort/complexity

**Velocity**: Average story points completed per sprint

**Burndown Chart**: Graph showing remaining work over time

**Sprint Goal**: What the team aims to achieve in a sprint

**Definition of Done**: Shared understanding of "complete"

**Retrospective**: Team meeting to reflect and improve

**Daily Standup**: Short daily sync (15 minutes)

**Sprint Planning**: Meeting to select sprint work (1-2 hours)

**Sprint Review**: Demo and feedback meeting (30-60 minutes)

### System Terms

**Hierarchy**: Parent-child relationships in backlog

**Immutable**: Cannot be changed (completed sprints)

**Snapshot**: Daily record of sprint progress

**Event Log**: Audit trail of all actions

**Computed Property**: Automatically calculated value (sprint points)

**Observer**: System component that triggers automatic updates

**Validation**: Checks to ensure data integrity

---

## Tips for Success

### First Sprint Setup

1. **Start Small**
   - 1-2 week sprint
   - 3-5 items maximum
   - Achievable goals

2. **Learn and Adjust**
   - Review what worked
   - Adjust next sprint
   - Don't expect perfection

3. **Build Rhythm**
   - Consistent sprint length
   - Regular ceremonies
   - Predictable cadence

### Long-Term Success

1. **Measure Everything**
   - Track velocity
   - Monitor completion rates
   - Review trends

2. **Continuous Improvement**
   - Act on retrospectives
   - Experiment with changes
   - Measure impact

3. **Stay Flexible**
   - Adapt processes
   - Respond to change
   - Focus on value

---

## Getting Help

### In-App Support

- Look for **ℹ️** icons for contextual help
- Hover over fields for tooltips
- Check validation messages for guidance

### Additional Resources

- **Implementation Plan**: Technical documentation
- **API Documentation**: For integrations
- **Admin Guide**: System configuration

### Contact Support

For technical issues:
- Email: support@projecttracker.com
- In-app: Help → Contact Support
- Documentation: docs.projecttracker.com

---

**Version**: 1.0  
**Last Updated**: September 25, 2026  
**For**: Project Tracker Agile Module  

---

*Happy Sprinting! 🚀*
