class Doctors extends React.Component {
    render() {
        return (
            <div>
            Start {this.props.name}
    </div>
    );
    }
}

React.render(
    <Doctors name="App" />,
    document.getElementById('main-body')
);