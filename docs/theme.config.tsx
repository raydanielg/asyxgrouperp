import React from 'react'
import { DocsThemeConfig } from 'nextra-theme-docs'

const config: DocsThemeConfig = {
  logo: <span style={{ fontWeight: 800 }}>ASYX<span style={{ color: '#d4a843' }}>GROUP</span> ERP</span>,
  project: {
    link: 'https://github.com/asyxgroup/erp',
  },
  docsRepositoryBase: 'https://github.com/asyxgroup/erp/tree/main/docs',
  footer: {
    text: '© 2024-2026 ASYX Group. All rights reserved.',
  },
  primaryHue: 155,
  primarySaturation: 60,
  sidebar: {
    defaultMenuCollapseLevel: 1,
    toggleButton: true,
  },
  toc: {
    title: 'On This Page',
  },
  editLink: {
    text: 'Edit this page →',
  },
  feedback: {
    content: 'Question? Give us feedback →',
  },
  head: (
    <>
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <meta property="og:title" content="ASYX Group ERP Documentation" />
      <meta property="og:description" content="Complete documentation for the ASYX Group ERP System" />
    </>
  ),
  useNextSeoProps() {
    return {
      titleTemplate: '%s – ASYX Group ERP Docs'
    }
  },
}

export default config
