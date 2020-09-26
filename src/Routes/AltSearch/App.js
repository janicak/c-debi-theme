import React, { useState, createContext } from 'react'
import { hot } from 'react-hot-loader'
import { ThemeProvider } from "styled-components"

import Titlebar from "./Titlebar"
import Main from "./Main"
import { getScrollBarWidth } from "./helpers"

export const AppContext = createContext({})

const theme = {
  textColor: "#32373c",
  borderColor: "#ccd0d4",
  darkBorderColor: "#7e8993",
  tableHeight: "calc(100vh - 230px)",
  tableHeaderHeight: 40,
  tableBodyCellHeight: 120,
  cellPaddingH: 10,
  cellPaddingV: 8,
  scrollBarWidth: getScrollBarWidth()
}

function App({ data: { title, items: initItems} }) {

  const [ items, setItems ] = useState(initItems)

  return (
    <AppContext.Provider value={{ items, setItems }}>
      <ThemeProvider theme={theme}>
        <Titlebar title={title}/>
        <Main items={items} />
      </ThemeProvider>
    </AppContext.Provider>
  )
}

export default hot(module)(App)