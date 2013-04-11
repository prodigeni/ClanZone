function T$(a)
{
	return document.getElementById(a)
}

function T$$(a, b)
{
	return b.getElementsByTagName(a)
}
var TINY = {};
TINY.slider = function ()
{
	function a(a, b)
	{
		this.n = a;
		this.init(b)
	}
	a.prototype.init = function (a)
	{
		var b = this.x = T$(a.id),
			c = this.u = T$$("ul", b)[0],
			d = this.m = T$$("li", c),
			e = d.length,
			f = this.l = this.c = 0;
		this.b = 1;
		if (a.navid && a.activeclass)
		{
			this.g = T$$("li", T$(a.navid));
			this.s = a.activeclass
		}
		this.a = a.auto || 0;
		this.p = a.resume || 0;
		this.r = a.rewind || 0;
		this.e = a.elastic || false;
		this.v = a.vertical || 0;
		b.style.overflow = "hidden";
		for (f; f < e; f++)
		{
			if (d[f].parentNode == c)
			{
				this.l++
			}
		}
		if (this.v)
		{
			c.style.top = 0;
			this.h = a.height || d[0].offsetHeight;
			c.style.height = this.l * this.h + "px"
		}
		else
		{
			c.style.left = 0;
			this.w = a.width || d[0].offsetWidth;
			c.style.width = this.l * this.w + "px"
		}
		this.nav(a.position || 0);
		if (a.position)
		{
			this.pos(a.position || 0, this.a ? 1 : 0, 1)
		}
		else if (this.a)
		{
			this.auto()
		}
		if (a.left)
		{
			this.sel(a.left)
		}
		if (a.right)
		{
			this.sel(a.right)
		}
	}, a.prototype.auto = function ()
	{
		this.x.ai = setInterval(new Function(this.n + ".move(1,1,1)"), this.a * 1e3)
	}, a.prototype.move = function (a, b)
	{
		var c = this.c + a;
		if (this.r)
		{
			c = a == 1 ? c == this.l ? 0 : c : c < 0 ? this.l - 1 : c
		}
		this.pos(c, b, 1)
	}, a.prototype.pos = function (a, b, c)
	{
		var d = a;
		clearInterval(this.x.ai);
		clearInterval(this.x.si);
		if (!this.r)
		{
			if (c)
			{
				if (a == -1 || a != 0 && Math.abs(a) % this.l == 0)
				{
					this.b++;
					for (var e = 0; e < this.l; e++)
					{
						this.u.appendChild(this.m[e].cloneNode(1))
					}
					this.v ? this.u.style.height = this.l * this.h * this.b + "px" : this.u.style.width = this.l * this.w * this.b + "px"
				}
				if (a == -1 || a < 0 && Math.abs(a) % this.l == 0)
				{
					this.v ? this.u.style.top = this.l * this.h * -1 + "px" : this.u.style.left = this.l * this.w * -1 + "px";
					d = this.l - 1
				}
			}
			else if (this.c > this.l && this.b > 1)
			{
				d = this.l * (this.b - 1) + a;
				a = d
			}
		}
		var f = this.v ? d * this.h * -1 : d * this.w * -1,
			g = a < this.c ? -1 : 1;
		this.c = d;
		var h = this.c % this.l;
		this.nav(h);
		if (this.e)
		{
			f = f - 8 * g
		}
		this.x.si = setInterval(new Function(this.n + ".slide(" + f + "," + g + ",1," + b + ")"), 10)
	}, a.prototype.nav = function (a)
	{
		if (this.g)
		{
			for (var b = 0; b < this.l; b++)
			{
				this.g[b].className = b == a ? this.s : ""
			}
		}
	}, a.prototype.slide = function (a, b, c, d)
	{
		var e = this.v ? parseInt(this.u.style.top) : parseInt(this.u.style.left);
		if (e == a)
		{
			clearInterval(this.x.si);
			if (this.e && c < 3)
			{
				this.x.si = setInterval(new Function(this.n + ".slide(" + (c == 1 ? a + 12 * b : a + 4 * b) + "," + (c == 1 ? -1 * b : -1 * b) + "," + (c == 1 ? 2 : 3) + "," + d + ")"), 10)
			}
			else
			{
				if (d || this.a && this.p)
				{
					this.auto()
				}
				if (this.b > 1 && this.c % this.l == 0)
				{
					this.clear()
				}
			}
		}
		else
		{
			var f = e - Math.ceil(Math.abs(a - e) * .1) * b + "px";
			this.v ? this.u.style.top = f : this.u.style.left = f
		}
	}, a.prototype.clear = function ()
	{
		var a = T$$("li", this.u),
			b = i = a.length;
		this.v ? this.u.style.top = 0 : this.u.style.left = 0;
		this.b = 1;
		this.c = 0;
		for (i; i > 0; i--)
		{
			var c = a[i - 1];
			if (b > this.l && c.parentNode == this.u)
			{
				this.u.removeChild(c);
				b--
			}
		}
	}, a.prototype.sel = function (a)
	{
		var b = T$(a);
		b.onselectstart = b.onmousedown = function ()
		{
			return false
		}
	};
	return {
		slide: a
	}
}()