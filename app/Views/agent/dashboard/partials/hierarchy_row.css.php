<style>
/**
 * Enhanced Hierarchy Tree CSS Styles
 *
 * Provides comprehensive styling for the progressive disclosure hierarchy tree UI.
 * Includes responsive design, accessibility features, and modern animations.
 *
 * Features:
 * - Mobile-first responsive design with breakpoints
 * - WCAG 2.1 AA accessibility compliance
 * - Smooth animations and hover effects
 * - Loading states and error handling styles
 * - Print-friendly styles
 * - High contrast and reduced motion support
 *
 * @author Real Estate Team
 * @version 2.0 - Enhanced progressive disclosure UI
 * @since 2025-08-16
 */

/* === CORE LAYOUT STYLES === */
.level-row {
  border-top: 1px solid #e9ecef;
  transition: opacity 0.3s ease;
}

.level-row.loading {
  opacity: 0.6;
  pointer-events: none;
}

/* === HOVER EFFECTS AND ANIMATIONS === */
.hover-lift {
  transition: all 0.2s ease;
  border: 1px solid transparent;
}

.hover-lift:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(0,0,0,.1);
  border-color: var(--primary-color, #2563eb);
}

.hover-lift:focus {
  outline: 2px solid var(--primary-color, #2563eb);
  outline-offset: 2px;
}

/* === AVATAR STYLING === */
.avatar {
  box-shadow: 0 2px 8px rgba(0,0,0,.1);
  transition: transform 0.2s ease;
}

.hover-lift:hover .avatar {
  transform: scale(1.05);
}

/* === EXPAND BUTTON STYLING === */
.expand-btn {
  transition: all 0.2s ease;
  min-width: 80px;
}

.expand-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(37, 99, 235, 0.2);
}

.expand-btn[aria-expanded="true"] {
  background-color: var(--primary-color, #2563eb);
  border-color: var(--primary-color, #2563eb);
  color: white;
}

.expand-btn[aria-expanded="true"] .fa-chevron-down {
  transform: rotate(180deg);
}

.expand-btn i {
  transition: transform 0.2s ease;
}

/* Badge styling */
.badge {
  font-size: 0.7rem;
  font-weight: 500;
}

/* Loading animation */
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.loading-placeholder {
  animation: pulse 1.5s ease-in-out infinite;
}

/* Responsive adjustments */
@media (max-width: 576px) {
  .level-row .card-body {
    padding: 0.75rem;
  }

  .avatar {
    width: 48px !important;
    height: 48px !important;
  }

  .avatar .fw-bold {
    font-size: 1rem !important;
  }

  .expand-btn {
    min-width: 70px;
    font-size: 0.8rem;
  }
}

/* === RESPONSIVE DESIGN === */
@media (max-width: 768px) {
  .level-row .row {
    justify-content: center;
  }

  .level-row .col-6 {
    flex: 0 0 calc(50% - 0.5rem);
    max-width: calc(50% - 0.5rem);
  }

  .agent-card .card-body {
    padding: 0.75rem 0.5rem;
  }

  .agent-card .avatar {
    width: 48px !important;
    height: 48px !important;
  }

  .agent-card .avatar .fw-bold {
    font-size: 1rem !important;
  }
}

@media (max-width: 480px) {
  .level-row .col-6 {
    flex: 0 0 100%;
    max-width: 100%;
  }

  .expand-btn {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    min-width: 60px;
  }

  .agent-card .position-absolute.top-0.start-0,
  .agent-card .position-absolute.top-0.end-0 {
    position: static !important;
    margin: 0 0 0.5rem 0;
    display: inline-block;
  }
}

/* === ACCESSIBILITY FEATURES === */
@media (prefers-reduced-motion: reduce) {
  .hover-lift,
  .avatar,
  .expand-btn,
  .expand-btn i,
  .level-row {
    transition: none !important;
    animation: none !important;
  }

  .loading-placeholder {
    animation: none;
  }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .hover-lift {
    border: 2px solid currentColor;
  }

  .badge {
    border: 1px solid currentColor;
  }

  .agent-card {
    border: 2px solid currentColor !important;
  }
}

/* Focus management for keyboard navigation */
.agent-card:focus-visible {
  outline: 3px solid var(--primary-color, #2563eb);
  outline-offset: 2px;
  transform: translateY(-2px);
}

.expand-btn:focus-visible {
  outline: 2px solid var(--primary-color, #2563eb);
  outline-offset: 2px;
}

/* Enhanced loading states */
.level-row.loading {
  position: relative;
}

.level-row.loading::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(1px);
  z-index: 5;
}

/* === PARENT AGENT DISPLAY STYLES === */
.parent-agent-info {
  background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
  border-radius: 6px;
  padding: 0.4rem 0.6rem;
  margin-bottom: 0.5rem;
  border-left: 3px solid var(--primary-color, #2563eb);
  transition: all 0.2s ease;
}

.parent-agent-info:hover {
  background: linear-gradient(135deg, #bbdefb 0%, #e1bee7 100%);
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(37, 99, 235, 0.15);
}

.primary-agent-badge {
  background: linear-gradient(135deg, #e8f5e8 0%, #f1f8e9 100%);
  border-radius: 6px;
  padding: 0.4rem 0.6rem;
  margin-bottom: 0.5rem;
  border-left: 3px solid #4caf50;
  transition: all 0.2s ease;
}

.primary-agent-badge:hover {
  background: linear-gradient(135deg, #c8e6c9 0%, #dcedc8 100%);
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(76, 175, 80, 0.15);
}

.parent-agent-name {
  font-weight: 600;
  color: var(--primary-color, #2563eb);
  text-decoration: none;
  transition: color 0.2s ease;
}

.parent-agent-name:hover {
  color: #1e40af;
  text-decoration: underline;
}

.primary-agent-text {
  font-weight: 600;
  color: #2e7d32;
}

/* Enhanced agent card styling for parent display */
.agent-card.has-parent {
  border-top: 2px solid var(--primary-color, #2563eb);
}

.agent-card.is-primary {
  border-top: 2px solid #4caf50;
}

/* Responsive adjustments for parent agent display */
@media (max-width: 576px) {
  .parent-agent-info,
  .primary-agent-badge {
    padding: 0.3rem 0.5rem;
    font-size: 0.8rem;
  }

  .parent-agent-name,
  .primary-agent-text {
    font-size: 0.8rem;
  }
}

/* Print styles */
@media print {
  .expand-btn,
  .position-absolute {
    display: none !important;
  }

  .agent-card {
    break-inside: avoid;
    box-shadow: none !important;
    border: 1px solid #000 !important;
  }

  .level-row {
    page-break-inside: avoid;
  }

  .parent-agent-info,
  .primary-agent-badge {
    background: #f5f5f5 !important;
    border-left: 2px solid #000 !important;
  }
}
</style>

