"use client"

import * as React from "react"
import * as RechartsPrimitive from "recharts"
import { cn } from "@/lib/utils"

// Chart container
const ChartContainer = React.forwardRef<
  HTMLDivElement,
  React.ComponentProps<"div"> & {
    config: Record<string, any>
  }
>(({ className, children, config, ...props }, ref) => (
  <div
    ref={ref}
    className={cn(
      "flex aspect-video justify-center text-xs", 
      className
    )}
    {...props}
  >
    <RechartsPrimitive.ResponsiveContainer width="100%" height="100%">
      {children}
    </RechartsPrimitive.ResponsiveContainer>
  </div>
))
ChartContainer.displayName = "ChartContainer"

// Chart tooltip
const ChartTooltip = RechartsPrimitive.Tooltip

// Chart tooltip content
const ChartTooltipContent = RechartsPrimitive.Tooltip

// Chart legend
const ChartLegend = RechartsPrimitive.Legend

// Chart legend content
const ChartLegendContent = RechartsPrimitive.Legend

// Chart pie
const ChartPie = RechartsPrimitive.PieChart
const ChartPieArc = RechartsPrimitive.Pie
const ChartPieCell = RechartsPrimitive.Cell

// Chart line
const ChartLine = RechartsPrimitive.LineChart
const ChartLineLine = RechartsPrimitive.Line
const ChartLineArea = RechartsPrimitive.Area
const ChartLineXAxis = RechartsPrimitive.XAxis
const ChartLineYAxis = RechartsPrimitive.YAxis
const ChartLineCartesianGrid = RechartsPrimitive.CartesianGrid

// Chart bar
const ChartBar = RechartsPrimitive.BarChart
const ChartBarBar = RechartsPrimitive.Bar
const ChartBarXAxis = RechartsPrimitive.XAxis
const ChartBarYAxis = RechartsPrimitive.YAxis
const ChartBarCartesianGrid = RechartsPrimitive.CartesianGrid

export {
  ChartContainer,
  ChartTooltip,
  ChartTooltipContent,
  ChartLegend,
  ChartLegendContent,
  ChartPie,
  ChartPieArc,
  ChartPieCell,
  ChartLine,
  ChartLineLine,
  ChartLineArea,
  ChartLineXAxis,
  ChartLineYAxis,
  ChartLineCartesianGrid,
  ChartBar,
  ChartBarBar,
  ChartBarXAxis,
  ChartBarYAxis,
  ChartBarCartesianGrid,
}
