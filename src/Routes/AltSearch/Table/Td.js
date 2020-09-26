import React, { useContext } from "react"
import styled, { ThemeContext } from "styled-components"

const StyledDiv = styled.div`
  color: ${props => props.theme.textColor};
  padding: ${props => `${props.theme.cellPaddingV}px ${props.theme.cellPaddingH}px`};
 
`

const Td = ({ cell, cellProps }) => {
  return (
    <StyledDiv {...cell.getCellProps(cellProps)} className="td">
      {cell.render('Cell')}
    </StyledDiv>
  )
}

export default Td