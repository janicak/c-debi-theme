import React, { useContext } from "react"
import styled, { ThemeContext } from "styled-components"

const StyledDiv = styled.div`
  padding: ${props => `${props.theme.cellPaddingV}px ${props.theme.cellPaddingH}`}px;
  margin-right: ${props => props.columnIndex + 1 === props.columnsLength ? props.theme.scrollBarWidth : '0'}px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  display: flex;
  align-items: center;
  height: 40px;
  
  .sorting-indicator {
    margin-top: 2px;
    visibility: visible;
  }
  
  .sorting-indicator:before {
    font: normal 20px/1 dashicons;
    speak: none;
    display: inline-block;
    padding: 0;
    position: relative;
    vertical-align: top;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-decoration: none!important;
    color: #444;
  } 
 
  &.asc a:focus span.sorting-indicator, &.asc:hover span.sorting-indicator, 
  &.desc a:focus span.sorting-indicator, &.desc:hover span.sorting-indicator, 
  &.sorted .sorting-indicator, &.sortable:hover .sorting-indicator {
      visibility: visible;
  }
  
  &.sorted.asc .sorting-indicator:before, &.sortable:hover .sorting-indicator:before  {
    content: "\\f142";
  } 
  &.sorted.desc .sorting-indicator:before, &.sorted.asc:hover .sorting-indicator:before {
      content: "\\f140";
  }
  &.sorted.desc:hover .sorting-indicator:before {
      content: "\\00D7";
      font-size: 14px;
      top: 2px;
      left: 6px;
  }
`

const Th = ({ column, headerProps }) => {
  let ThHeaderProps = column.getHeaderProps(column.getSortByToggleProps(headerProps));

  const ThClassNames = `th ${ column.disableSortBy ? "" : column.isSorted ? column.isSortedDesc ? " sorted desc" : " sorted asc" : " sortable"}`

  return (
    <StyledDiv {...ThHeaderProps} className={ThClassNames}>
      { column.disableSortBy
        ? <span>{column.render("Header")}</span>
        : <a>{column.render("Header")}</a>
      }
      <span className="sorting-indicator"/>
    </StyledDiv>
  )
}

export default Th